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

    public function __construct()
    {
        $this->setRepository(SimproJobRepository::class);

        $this->jobService = app(JobService::class);
        $this->simproCustomerService = app(SimproCustomerService::class);
        $this->scheduleService = app(ScheduleService::class);
        $this->simproSiteService = app(SimproSiteService::class);
    }

    public function handleJob($webhook)
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
            case "company.customer.created":
            case "company.customer.updated":
                $this->simproCustomerService->createOrUpdateBySimpro($webhook, SimproCustomer::TYPE_COMPANIES);
                break;
            case "company.customer.deleted":
                $this->simproCustomerService->deleteBySimpro($webhook, SimproCustomer::TYPE_COMPANIES);
                break;
            case "individual.customer.created":
            case "individual.customer.updated":
                $this->simproCustomerService->createOrUpdateBySimpro($webhook, SimproCustomer::TYPE_INDIVIDUALS);
                break;
            case "individual.customer.deleted":
                $this->simproCustomerService->deleteBySimpro($webhook, SimproCustomer::TYPE_INDIVIDUALS);
                break;
        }
    }
}
