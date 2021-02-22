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
    protected SimproCustomerService $simproCustomerService;

    public function __construct()
    {
        $this->setRepository(SimproJobRepository::class);

        $this->simproCustomerService = app(SimproCustomerService::class);
    }

    public function handleJob($webhook)
    {
        $event = $webhook['data']['ID'];

        switch ($event) {
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
