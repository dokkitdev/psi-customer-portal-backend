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
                return $this->jobService->createOrUpdateBySimpro($webhook);
            case 'job.deleted':
                return $this->jobService->deleteBySimpro($webhook);
            case 'job.schedule.created':
            case 'job.schedule.updated':
                return $this->scheduleService->updateOrCreateBySimpro($webhook);
            case 'job.schedule.deleted':
                return $this->scheduleService->deleteBySimpro($webhook);
            case 'site.created':
            case 'site.updated':
                return $this->simproSiteService->createOrUpdateBySimpro($webhook);
            case 'company.customer.created':
            case 'company.customer.updated':
                return $this->simproCustomerService->createOrUpdateBySimpro($webhook, SimproCustomer::TYPE_COMPANIES);
            case 'company.customer.deleted':
                return $this->simproCustomerService->deleteBySimpro($webhook, SimproCustomer::TYPE_COMPANIES);
            case 'individual.customer.created':
            case 'individual.customer.updated':
                return $this->simproCustomerService->createOrUpdateBySimpro($webhook, SimproCustomer::TYPE_INDIVIDUALS);
            case 'individual.customer.deleted':
                return $this->simproCustomerService->deleteBySimpro($webhook, SimproCustomer::TYPE_INDIVIDUALS);
            default: return true;
        }
    }
}
