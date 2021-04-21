<?php

namespace App\Console\Commands\Simpro;

use App\Models\InvoiceLog;
use App\Services\InvoiceLogService;
use App\Services\InvoiceService;
use Illuminate\Console\Command;
use Exception;

class HandleInvoicesLog extends Command
{
    protected $signature = 'invoices-log:handle';

    protected $description = 'Handle Invoices Log';

    protected InvoiceLogService $invoiceLogService;
    protected InvoiceService $invoiceService;

    public function handle()
    {
        $this->invoiceLogService = app(InvoiceLogService::class);
        $this->invoiceService = app(InvoiceService::class);

        $this->invoiceLogService
            ->getForHandle(1000)
            ->each(function ($invoiceLog) {
                try {
                    $this->invoiceService->updateOrCreateBySimpro(0, $invoiceLog['invoice_id']);

                    $this->invoiceLogService->delete($invoiceLog['id']);
                } catch (Exception $e) {
                    report($e);

                    $this->invoiceLogService->update($invoiceLog['id'], [
                        'handle_status' => InvoiceLog::HANDLE_STATUS_ERROR,
                        'handle_result' => [
                            'code' => $e->getCode(),
                            'message' => $e->getMessage()
                        ]
                    ]);
                }
            });
    }
}
