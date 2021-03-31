<?php

namespace App\Console\Commands;

use App\ApiClients\SimproApiClient;
use App\Services\JobService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class UpdateJobsHandler extends Command
{
    protected $signature = 'simpro:update-jobs';

    protected $description = 'Update Simpro jobs';

    protected JobService $jobService;
    protected SimproApiClient $simproClient;

    public function handle()
    {
        $this->simproClient = app(SimproApiClient::class);
        $this->jobService = app(JobService::class);

        $this->jobService->chunk(1000, function ($jobs) {
            foreach ($jobs as $job) {
                try {
                    $jobFromSimpro = $this->simproClient->getJob(0, $job['job_id']);
                    $this->jobService->update($job['id'], [
                        'name' => Arr::get($jobFromSimpro, 'Name')
                    ]);
                } catch (Exception $e) {
                    report($e);
                }
            }
        });

        $this->line('Simpro Jobs updated');
    }
}
