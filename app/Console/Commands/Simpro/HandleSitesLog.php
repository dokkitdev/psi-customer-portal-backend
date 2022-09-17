<?php

namespace App\Console\Commands\Simpro;

use App\Console\Commands\TimeoutCommand;
use App\Services\SiteLogService;

class HandleSitesLog extends TimeoutCommand
{
    protected $signature = 'sites-log:handle';

    protected $description = 'Handle Sites Log';

    public function handle()
    {
        app(SiteLogService::class)->handleLog();
    }
}
