<?php

namespace App\Console\Commands\Simpro;

use App\Console\Commands\TimeoutCommand;
use App\Services\InvoiceLogService;

class HandleInvoicesLog extends TimeoutCommand
{
    protected $signature = 'invoices-log:handle';

    protected $description = 'Handle Invoices Log';

    public function handle()
    {
        app(InvoiceLogService::class)->handleLog();
    }
}
