<?php

namespace App\Console\Commands\Simpro;

use App\Services\InvoiceLogService;
use Illuminate\Console\Command;

class SaveInvoicesToLog extends Command
{
    protected $signature = 'simpro:save-invoices-to-log';

    protected $description = 'Save Simpro Invoices to InvoiceLogs table';

    public function handle()
    {
        app(InvoiceLogService::class)->saveAllInvoices();

        $this->line('Simpro Invoices saved');
    }
}
