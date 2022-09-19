<?php

namespace App\Console\Commands\Simpro;

use App\Console\Commands\TimeoutCommand;
use App\Services\QuoteLogService;

class HandleQuotesLog extends TimeoutCommand
{
    protected $signature = 'quotes-log:handle';

    protected $description = 'Handle Quotes Log';

    public function handle()
    {
        app(QuoteLogService::class)->handleLog();
    }
}
