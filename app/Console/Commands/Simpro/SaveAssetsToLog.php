<?php

namespace App\Console\Commands\Simpro;

use App\Services\AssetLogService;
use Illuminate\Console\Command;

class SaveAssetsToLog extends Command
{
    protected $signature = 'simpro:save-assets-to-log';

    protected $description = 'Save Simpro Assets to AssetLogs table';

    public function handle()
    {
        app(AssetLogService::class)->saveAllAssets();

        $this->line('Simpro Assets saved');
    }
}
