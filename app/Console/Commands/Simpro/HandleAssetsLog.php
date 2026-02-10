<?php

namespace App\Console\Commands\Simpro;

use App\Console\Commands\TimeoutCommand;
use App\Services\AssetLogService;

class HandleAssetsLog extends TimeoutCommand
{
    protected $signature = 'assets-log:handle';

    protected $description = 'Handle Assets Log';

    public function handle()
    {
        app(AssetLogService::class)->handleLog();
    }
}
