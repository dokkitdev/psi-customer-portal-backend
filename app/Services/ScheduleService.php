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

        $job = $this->jobService->findBy('job_id', $jobId);

        if (!$job) {
            return true;
        }

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
        return $this->repository->updateOrCreate([
            'job_id' => $jobId,
            'schedule_id' => $schedule['ID']
        ], [
            'name' => Arr::get($schedule, 'Staff.Name'),
            'date' => $this->prepareDate($schedule),
            'start_time' => Arr::get($schedule, 'Blocks.0.ISO8601StartTime'),
            'end_time' => Arr::get($schedule, 'Blocks.0.ISO8601EndTime')
        ]);
    }

    protected function setRecentScheduleToJob($jobId)
    {
        $recentScheduleId = null;

        $recentSchedule = $this->repository->getRecentSchedule($jobId);

        if ($recentSchedule) {
            $recentScheduleId = $recentSchedule['id'];
        }

        $this->jobService->update($jobId, [
            'recent_schedule_id' => $recentScheduleId
        ]);
    }

    protected function prepareDate($schedule)
    {
        $date = Arr::get($schedule, 'Date');
        $startTime = Arr::get($schedule, 'Blocks.0.StartTime');

        if ($startTime) {
            $date = "{$date} {$startTime}:00";
        }

        return $date;
    }
}
