<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\InvoiceLog;
use App\Repositories\InvoiceLogRepository;
use Exception;
use RonasIT\Support\Services\EntityService;

/**
 * @property InvoiceLogRepository $repository
 * @mixin InvoiceLogRepository
 */
class InvoiceLogService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected InvoiceService $invoiceService;
    protected $companyId;

    public function __construct()
    {
        $this->setRepository(InvoiceLogRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->invoiceService = app(InvoiceService::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterBy('handle_status')
            ->getSearchResults();
    }

    public function saveAllInvoices()
    {
        $invoicePages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/customerInvoices/");

        foreach ($invoicePages as $invoicePage) {
            foreach ($invoicePage as $invoiceFromSimpro) {
                $this->repository->updateOrCreate(['invoice_id' => $invoiceFromSimpro['ID']], []);
            }
        }
    }

    public function handleLog()
    {
        $invoiceLogs = $this->search([
            'handle_status' => InvoiceLog::HANDLE_STATUS_NEW,
            'page' => 1,
            'per_page' => 1000,
            'order_by' => 'id',
        ]);

        foreach ($invoiceLogs['data'] as $invoiceLog) {
            try {
                $this->invoiceService->updateOrCreateBySimpro(0, $invoiceLog['invoice_id']);

                $this->delete($invoiceLog['id']);
            } catch (Exception $e) {
                report($e);

                $this->update($invoiceLog['id'], [
                    'handle_status' => InvoiceLog::HANDLE_STATUS_ERROR,
                    'handle_result' => [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage()
                    ]
                ]);
            }
        }
    }
}
