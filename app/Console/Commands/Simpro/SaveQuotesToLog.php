<?php

namespace App\Console\Commands\Simpro;

use App\Services\QuoteLogService;
use Illuminate\Console\Command;

class SaveQuotesToLog extends Command
{
    protected $signature = 'simpro:save-quotes-to-log';

    protected $description = 'Save Simpro Quotes to QuoteLogs table';

    public function handle()
    {
        app(QuoteLogService::class)->saveAllQuotes();

        $this->line('Simpro Quotes saved');
    }
}
