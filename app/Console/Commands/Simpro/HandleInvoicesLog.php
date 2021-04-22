<?php

namespace App\Console\Commands\Simpro;

use App\Services\InvoiceLogService;
use Illuminate\Console\Command;

class HandleInvoicesLog extends Command
{
    protected $signature = 'invoices-log:handle';

    protected $description = 'Handle Invoices Log';

    public function handle()
    {
        app(InvoiceLogService::class)->handleLog();
    }
}
