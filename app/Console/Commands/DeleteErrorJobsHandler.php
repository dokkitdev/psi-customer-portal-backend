<?php

namespace App\Console\Commands;

use App\Services\SimproJobService;
use Illuminate\Console\Command;

class DeleteErrorJobsHandler extends Command
{
    protected $signature = 'simpro:delete-error-jobs';

    protected $description = 'Delete Simpro error jobs';

    public function handle()
    {
        $date = now()->subMonths(1);
        app(SimproJobService::class)->deleteErrorJobs($date);
    }
}
