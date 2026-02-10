<?php

namespace App\Console\Commands\Simpro;

use App\Services\SiteLogService;
use Illuminate\Console\Command;

class SaveSitesToLog extends Command
{
    protected $signature = 'simpro:save-sites-to-log';

    protected $description = 'Save Simpro Sites to SiteLogs table';

    public function handle()
    {
        app(SiteLogService::class)->saveAllSites();

        $this->line('Simpro Sites saved');
    }
}
