<?php

namespace App\Console\Commands\Simpro;

use App\Console\Commands\TimeoutCommand;
use App\Services\AssetLogService;

class SaveAssetsToLog extends TimeoutCommand
{
    protected $signature = 'simpro:save-assets-to-log';

    protected $description = 'Save Simpro Assets to AssetLogs table';

    public function handle()
    {
        app(AssetLogService::class)->saveAllAssets();

        $this->line('Simpro Assets saved');
    }
}
