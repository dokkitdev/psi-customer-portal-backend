<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @property InvoiceRepository $repository
 * @mixin InvoiceRepository
 */
class InvoiceService extends BaseService
{
    protected SimproApiClient $simproClient;
    protected JobAttachmentService $jobAttachmentService;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(InvoiceRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->jobAttachmentService = app(JobAttachmentService::class);
    }

    public function search($filters)
    {
        $authUser = $this->getAuthUser();

        if ($authUser['role_id'] === Role::USER) {
            $filters['site_has_user'] = $authUser['id'];
        }

        return $this->repository
            ->searchQuery($filters)
            ->filterByIntQuery('invoice_id')
            ->filterBy('job_id')
            ->filterByIntQuery('job.job_id', 'simpro_job_id')
            ->filterBy('job.simpro_site_id')
            ->filterBy('job.simpro_customer_id')
            ->filterByList('status', 'statuses')
            ->filterBy('total')
            ->filterFrom('total', false, 'total_from')
            ->filterTo('total', false, 'total_to')
            ->filterBy('date_issued')
            ->filterFrom('date_issued', false, 'date_issued_from')
            ->filterTo('date_issued', false, 'date_issued_to')
            ->filterBy('date_paid')
            ->filterFrom('date_paid', false, 'date_paid_from')
            ->filterTo('date_paid', false, 'date_paid_to')
            ->filterBy('is_paid')
            ->filterByUserGroups()
            ->with()
            ->getSearchResults();
    }

    public function syncBySimpro($companyId, $jobIdFromSimpro, $jobId)
    {
        $invoicesFromSimproPages = $this->simproClient->getAsGenerator(
            "companies/{$companyId}/jobs/$jobIdFromSimpro/invoices/",
            ['columns' => 'ID,DateIssued,Stage,Total']
        );

        $invoices = $this->repository->get(['job_id' => $jobId]);

        foreach ($invoicesFromSimproPages as $invoicesFromSimproPage) {
            foreach ($invoicesFromSimproPage as $invoiceFromSimpro) {
                $invoiceFromSimproId = $invoiceFromSimpro['ID'];
                $customerInvoice = $this->simproClient->getCustomerInvoice($companyId, $invoiceFromSimproId);
                $attachment = $this->findMostRecentAttachment($jobId, $invoiceFromSimproId);
                $data = [
                    'job_id' => $jobId,
                    'invoice_id' => $invoiceFromSimproId,
                    'date_issued' => $invoiceFromSimpro['DateIssued'],
                    'status' => $invoiceFromSimpro['Stage'],
                    'total' => Arr::get($invoiceFromSimpro, 'Total.ExTax'),
                    'date_paid' => !empty(trim($customerInvoice['DatePaid'])) ? $customerInvoice['DatePaid'] : null,
                    'is_paid' => $customerInvoice['IsPaid'],
                    'job_attachment_id' => Arr::get($attachment, 'id')
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

    public function updateOrCreateBySimpro($companyId, $invoiceFromSimproId)
    {
        $customerInvoice = $this->simproClient->getCustomerInvoice($companyId, $invoiceFromSimproId);

        $job = app(JobService::class)->getOrCreateBySimpro($companyId, Arr::get($customerInvoice, 'Jobs.0.ID'));

        $attachment = $this->findMostRecentAttachment($job['id'], $invoiceFromSimproId);

        return $this->repository->updateOrCreate([
            'job_id' => $job['id'],
            'invoice_id' => $invoiceFromSimproId,
        ], [
            'date_issued' => $customerInvoice['DateIssued'],
            'status' => $customerInvoice['Stage'],
            'total' => Arr::get($customerInvoice, 'Total.ExTax'),
            'date_paid' => !empty(trim($customerInvoice['DatePaid'])) ? $customerInvoice['DatePaid'] : null,
            'is_paid' => $customerInvoice['IsPaid'],
            'job_attachment_id' => Arr::get($attachment, 'id')
        ]);
    }

    protected function findMostRecentAttachment($jobId, $invoiceFromSimproId)
    {
        $attachments = $this->jobAttachmentService->get(['job_id' => $jobId]);

        $needles = "invoice_no_{$invoiceFromSimproId}_";

        return collect($attachments)->sortByDesc('date_added')->first(function ($attachment) use ($needles) {
            $lowerFilename = Str::lower($attachment['name']);

            return Str::contains($lowerFilename, $needles);
        });
    }
}
