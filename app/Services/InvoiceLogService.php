<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\InvoiceLogRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property InvoiceLogRepository $repository
 * @mixin InvoiceLogRepository
 */
class InvoiceLogService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected $companyId;

    public function __construct()
    {
        $this->setRepository(InvoiceLogRepository::class);

        $this->simproClient = app(SimproApiClient::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function saveAllInvoices()
    {
        $invoicePages = $this->simproClient->getInvoicesAsGenerator($this->companyId);

        foreach ($invoicePages as $invoicePage) {
            foreach ($invoicePage as $invoiceFromSimpro) {
                $this->createOrUpdate($invoiceFromSimpro);
            }
        }
    }

    protected function createOrUpdate($invoice)
    {
        return $this->repository->updateOrCreate(['invoice_id' => $invoice['ID']], []);
    }
}
