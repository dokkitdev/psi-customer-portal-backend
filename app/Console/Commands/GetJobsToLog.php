<?php

namespace App\Console\Commands;

use App\Services\JobLogService;
use Illuminate\Console\Command;

class GetJobsToLog extends Command
{
    protected $signature = 'simpro:get-jobs-to-log';

    protected $description = 'Get Simpro Jobs to JobLogs table';

    public function handle()
    {
        app(JobLogService::class)->saveAllJobs();

        $this->line('Simpro Jobs saved');
    }
}
