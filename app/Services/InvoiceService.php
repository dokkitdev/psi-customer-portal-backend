<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\Arr;

/**
 * @property InvoiceRepository $repository
 * @mixin InvoiceRepository
 */
class InvoiceService extends BaseService
{
    protected SimproApiClient $simproClient;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(InvoiceRepository::class);

        $this->simproClient = app(SimproApiClient::class);
    }

    public function search($filters)
    {
        $authUser = $this->getAuthUser();

        if ($authUser['role_id'] === Role::USER) {
            $filters['site_has_user'] = $authUser['id'];
        }

        return $this->repository
            ->searchQuery($filters)
            ->filterByUserGroups()
            ->with()
            ->getSearchResults();
    }

    public function syncBySimpro($companyId, $jobIdFromSimpro, $jobId)
    {
        $invoicesFromSimproPages = $this->simproClient->getJobInvoicesAsGenerator($companyId, $jobIdFromSimpro);

        $invoices = $this->repository->get(['job_id' => $jobId]);

        foreach ($invoicesFromSimproPages as $invoicesFromSimproPage) {
            foreach ($invoicesFromSimproPage as $invoiceFromSimpro) {
                $invoiceFromSimproId = $invoiceFromSimpro['ID'];
                $customerInvoice = $this->simproClient->getCustomerInvoice($companyId, $invoiceFromSimproId);
                $data = [
                    'job_id' => $jobId,
                    'invoice_id' => $invoiceFromSimproId,
                    'date_issued' => $invoiceFromSimpro['DateIssued'],
                    'status' => $invoiceFromSimpro['Stage'],
                    'total' => Arr::get($invoiceFromSimpro, 'Total.ExTax'),
                    'date_paid' => !empty(trim($customerInvoice['DatePaid'])) ? $customerInvoice['DatePaid'] : null
                ];
                $invoice = $invoices->firstWhere('invoice_id', $invoiceFromSimproId);
                if ($invoice) {
                    $this->repository->update($invoice['id'], $data);
                    $invoices = $invoices->where('id', '!=', $invoice['id']);
                } else {
                    $this->repository->create($data);
                }
            }
        }

        if ($invoices->isNotEmpty()) {
            $ids = $invoices->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }
}
