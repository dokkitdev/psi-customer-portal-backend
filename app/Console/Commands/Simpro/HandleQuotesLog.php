<?php

namespace App\Console\Commands\Simpro;

use App\Services\QuoteLogService;
use Illuminate\Console\Command;

class HandleQuotesLog extends Command
{
    protected $signature = 'quotes-log:handle';

    protected $description = 'Handle Quotes Log';

    public function handle()
    {
        app(QuoteLogService::class)->handleLog();
    }
}
