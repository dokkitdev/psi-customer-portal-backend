<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\ScheduleRepository;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\EntityService;

/**
 * @property ScheduleRepository $repository
 * @mixin ScheduleRepository
 */
class ScheduleService extends EntityService
{
    protected JobService $jobService;
    protected SimproApiClient $simproClient;

    public function __construct()
    {
        $this->setRepository(ScheduleRepository::class);

        $this->jobService = app(JobService::class);
        $this->simproClient = app(SimproApiClient::class);
    }

    public function createOrUpdateManyBySimpro($companyId, $jobIdFromSimpro, $jobId)
    {
        $schedulePages = $this->simproClient->getSchedulesAsGenerator($companyId, $jobIdFromSimpro);

        foreach ($schedulePages as $schedulePage) {
            foreach ($schedulePage as $scheduleFromSimpro) {
                $this->createOrUpdate($scheduleFromSimpro, $jobId);
            }
        }

        $this->setRecentScheduleToJob($jobId);
    }

    public function updateOrCreateBySimpro($webhook)
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $jobId = $webhook['data']['reference']['jobID'];
        $scheduleId = $webhook['data']['reference']['scheduleID'];

        $job = $this->jobService->getOrCreateBySimpro($companyId, $jobId);

        $scheduleFromSimpro = $this->simproClient->getSchedule($companyId, $scheduleId);

        $schedule = $this->createOrUpdate($scheduleFromSimpro, $job['id']);

        $this->setRecentScheduleToJob($job['id']);

        return $schedule;
    }

    public function deleteBySimpro($webhook)
    {
        $jobId = $webhook['data']['reference']['jobID'];
        $scheduleId = $webhook['data']['reference']['scheduleID'];

        $job = $this->jobService->findBy('job_id', $jobId);

        if (!$job) {
            return true;
        }

        $result = $this->repository->delete([
            'job_id' => $job['id'],
            'schedule_id' => $scheduleId
        ]);

        $this->setRecentScheduleToJob($job['id']);

        return $result;
    }

    protected function createOrUpdate($schedule, $jobId)
    {
        $block = $this->findBlock(Arr::get($schedule, 'Blocks', []));

        return $this->repository->updateOrCreate([
            'job_id' => $jobId,
            'schedule_id' => $schedule['ID']
        ], [
            'name' => (Arr::get($schedule, 'Staff.Type') === 'employee') ? Arr::get($schedule, 'Staff.Name') : 'Other Engineer',
            'date' => $this->prepareDate($schedule, $block),
            'start_time' => Arr::get($block, 'ISO8601StartTime'),
            'end_time' => Arr::get($block, 'ISO8601EndTime')
        ]);
    }

    protected function setRecentScheduleToJob($jobId)
    {
        $recentSchedule = $this->repository->getRecentSchedule($jobId);

        $this->jobService->update($jobId, [
            'recent_schedule_id' => Arr::get($recentSchedule, 'id')
        ]);
    }

    protected function prepareDate($schedule, $block)
    {
        $date = Arr::get($schedule, 'Date');
        $startTime = Arr::get($block, 'StartTime');

        if ($startTime) {
            $date = "{$date} {$startTime}:00";
        }

        return $date;
    }

    protected function findBlock($blocks)
    {
        return collect($blocks)->sortBy('StartTime')->first(null, []);
    }
}
