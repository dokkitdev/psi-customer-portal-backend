<?php

namespace App\Console\Commands\Simpro;

use App\Services\SiteLogService;
use Illuminate\Console\Command;

class HandleSitesLog extends Command
{
    protected $signature = 'sites-log:handle';

    protected $description = 'Handle Sites Log';

    public function handle()
    {
        app(SiteLogService::class)->handleLog();
    }
}
