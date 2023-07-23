<?php

namespace App\Services;

use App\Models\SimproCustomer;
use App\Repositories\SimproJobRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property SimproJobRepository $repository
 * @mixin SimproJobRepository
 */
class SimproJobService extends EntityService
{
    protected JobService $jobService;
    protected SimproCustomerService $simproCustomerService;
    protected ScheduleService $scheduleService;
    protected SimproSiteService $simproSiteService;
    protected QuoteService $quoteService;
    protected AssetService $assetService;
    protected JobAttachmentService $jobAttachmentService;
    protected InvoiceService $invoiceService;

    public function __construct()
    {
        $this->setRepository(SimproJobRepository::class);

        $this->jobService = app(JobService::class);
        $this->simproCustomerService = app(SimproCustomerService::class);
        $this->scheduleService = app(ScheduleService::class);
        $this->simproSiteService = app(SimproSiteService::class);
        $this->quoteService = app(QuoteService::class);
        $this->assetService = app(AssetService::class);
        $this->jobAttachmentService = app(JobAttachmentService::class);
        $this->invoiceService = app(InvoiceService::class);
    }

    public function handleJob($webhook): void
    {
        $event = $webhook['data']['ID'];

        switch ($event) {
            case 'job.created':
            case 'job.updated':
                $this->jobService->createOrUpdateBySimpro($webhook);
                break;
            case 'job.deleted':
                $this->jobService->deleteBySimpro($webhook);
                break;
            case 'job.schedule.created':
            case 'job.schedule.updated':
                $this->scheduleService->updateOrCreateBySimpro($webhook);
                break;
            case 'job.schedule.deleted':
                $this->scheduleService->deleteBySimpro($webhook);
                break;
            case 'site.created':
            case 'site.updated':
                $this->simproSiteService->createOrUpdateBySimpro($webhook);
                break;
            case 'site.deleted':
                $this->simproSiteService->deleteBySimpro($webhook);
                break;
            case 'quote.created':
            case 'quote.updated':
                $this->quoteService->updateOrCreateBySimpro($webhook);
                break;
            case 'quote.deleted':
                $this->quoteService->deleteBySimpro($webhook);
                break;
            case 'asset.created':
            case 'asset.updated':
                $this->assetService->updateOrCreateBySimpro($webhook);
                break;
            case 'asset.deleted':
                $this->assetService->deleteBySimpro($webhook);
                break;
            case 'company.customer.created':
            case 'company.customer.updated':
                $this->simproCustomerService->createOrUpdateBySimpro($webhook, SimproCustomer::TYPE_COMPANIES);
                break;
            case 'company.customer.deleted':
                $this->simproCustomerService->deleteBySimpro($webhook, SimproCustomer::TYPE_COMPANIES);
                break;
            case 'individual.customer.created':
            case 'individual.customer.updated':
                $this->simproCustomerService->createOrUpdateBySimpro($webhook, SimproCustomer::TYPE_INDIVIDUALS);
                break;
            case 'individual.customer.deleted':
                $this->simproCustomerService->deleteBySimpro($webhook, SimproCustomer::TYPE_INDIVIDUALS);
                break;
            case 'job.attachment.created':
            case 'job.attachment.updated':
                $this->jobAttachmentService->createOrUpdateBySimpro($webhook);
                break;
            case 'job.attachment.deleted':
                $this->jobAttachmentService->deleteBySimpro($webhook);
                break;
            case 'invoice.created':
            case 'invoice.status':
            case 'invoice.updated':
                $companyId = $webhook['data']['reference']['companyID'];
                $invoiceId = $webhook['data']['reference']['invoiceID'];

                $this->invoiceService->updateOrCreateBySimpro($companyId, $invoiceId);
                break;
            case 'invoice.deleted':
                $this->invoiceService->delete([
                    'invoice_id' => $webhook['data']['reference']['invoiceID'],
                ]);
                break;
            default:
                //DO NOTHING
        }
    }
}
