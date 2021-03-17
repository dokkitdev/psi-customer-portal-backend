<?php

namespace App\Console\Commands;

use App\Models\JobLog;
use App\Services\JobLogService;
use App\Services\JobService;
use Illuminate\Console\Command;
use Exception;

class HandleJobsLog extends Command
{
    protected $signature = 'jobs-log:handle';

    protected $description = 'Handle Jobs Log';

    protected JobLogService $jobLogService;
    protected JobService $jobService;

    public function handle()
    {
        $this->jobLogService = app(JobLogService::class);
        $this->jobService = app(JobService::class);

        $this->jobLogService
            ->getForHandle(1000)
            ->each(function ($jobLog) {
                try {
                    $this->jobService->createOrUpdateBySimpro([
                        'data' => [
                            'reference' => [
                                'companyID' => 0,
                                'jobID' => $jobLog['job_id']
                            ]
                        ]
                    ]);

                    $this->jobLogService->delete($jobLog['id']);
                } catch (Exception $e) {
                    report($e);

                    $this->jobLogService->update($jobLog['id'], [
                        'handle_status' => JobLog::HANDLE_STATUS_ERROR,
                        'handle_result' => [
                            'code' => $e->getCode(),
                            'message' => $e->getMessage()
                        ]
                    ]);
                }
            });
    }
}
