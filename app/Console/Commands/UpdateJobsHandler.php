<?php

namespace App\Console\Commands;

use App\ApiClients\SimproApiClient;
use App\Services\JobService;
use App\Services\JobWorkOrderService;
use App\Services\ScheduleService;
use Exception;
use Illuminate\Console\Command;

class UpdateJobsHandler extends Command
{
    protected $signature = 'simpro:update-jobs';

    protected $description = 'Update Simpro jobs';

    protected ScheduleService $scheduleService;
    protected JobWorkOrderService $jobWorkOrderService;
    protected JobService $jobService;
    protected SimproApiClient $simproClient;

    public function handle()
    {
        $this->simproClient = app(SimproApiClient::class);
        $this->jobService = app(JobService::class);
        $this->scheduleService = app(ScheduleService::class);
        $this->jobWorkOrderService = app(JobWorkOrderService::class);

        $jobs = $this->jobService->get();

        foreach ($jobs as $job) {
            try {
                $this->scheduleService->createOrUpdateManyBySimpro(0, $job['job_id'], $job['id']);

                $jobFromSimpro = $this->simproClient->getJob(0, $job['job_id']);
                $this->jobWorkOrderService->syncBySimpro(0, $jobFromSimpro, $job['id']);
            } catch (Exception $e) {
                report($e);
            }
        }

        $this->line('Simpro Jobs updated');
    }
}
