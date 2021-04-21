<?php

namespace App\Console\Commands\Simpro;

use App\Services\SiteLogService;
use Illuminate\Console\Command;

class GetSitesToLog extends Command
{
    protected $signature = 'simpro:get-sites-to-log';

    protected $description = 'Get Simpro Sites to SiteLogs table';

    public function handle()
    {
        app(SiteLogService::class)->saveAllSites();

        $this->line('Simpro Sites saved');
    }
}
