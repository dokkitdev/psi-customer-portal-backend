<?php

namespace App\Console\Commands\Simpro;

use App\Services\QuoteLogService;
use Illuminate\Console\Command;

class GetQuotesToLog extends Command
{
    protected $signature = 'simpro:get-quotes-to-log';

    protected $description = 'Get Simpro Quotes to QuoteLogs table';

    public function handle()
    {
        app(QuoteLogService::class)->saveAllQuotes();

        $this->line('Simpro Quotes saved');
    }
}
