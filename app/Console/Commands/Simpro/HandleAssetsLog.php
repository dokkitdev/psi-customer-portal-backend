<?php

namespace App\Console\Commands\Simpro;

use App\Services\AssetLogService;
use Illuminate\Console\Command;

class HandleAssetsLog extends Command
{
    protected $signature = 'assets-log:handle';

    protected $description = 'Handle Assets Log';

    public function handle()
    {
        app(AssetLogService::class)->handleLog();
    }
}
