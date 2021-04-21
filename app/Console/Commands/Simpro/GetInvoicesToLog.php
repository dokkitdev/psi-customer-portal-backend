<?php

namespace App\Console\Commands\Simpro;

use App\Services\InvoiceLogService;
use Illuminate\Console\Command;

class GetInvoicesToLog extends Command
{
    protected $signature = 'simpro:get-invoices-to-log';

    protected $description = 'Get Simpro Invoices to InvoiceLogs table';

    public function handle()
    {
        app(InvoiceLogService::class)->saveAllInvoices();

        $this->line('Simpro Invoices saved');
    }
}
