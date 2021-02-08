<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\SimproCustomer;
use App\Repositories\SimproCustomerRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property SimproCustomerRepository $repository
 * @mixin SimproCustomerRepository
 */
class SimproCustomerService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected $companyId;

    public function __construct()
    {
        $this->setRepository(SimproCustomerRepository::class);

        $this->simproClient = app(SimproApiClient::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterByQuery(['name'])
            ->getSearchResults();
    }

    public function matchCustomers()
    {
        $pageSize = 250;

        $companies = $this->simproClient->getAll($pageSize, function ($pageSize, $page) {
            return $this->simproClient->getCustomers($this->companyId, SimproCustomer::TYPE_COMPANIES, [
                'pageSize' => $pageSize,
                'page' => $page
            ]);
        });

        $individuals = $this->simproClient->getAll($pageSize, function ($pageSize, $page) {
            return $this->simproClient->getCustomers($this->companyId, SimproCustomer::TYPE_INDIVIDUALS, [
                'pageSize' => $pageSize,
                'page' => $page
            ]);
        });

        $companiesMapped = collect($companies)->map(function ($company) {
            return [
                'customer_id' => $company['ID'],
                'name' => $company['CompanyName'],
                'type' => SimproCustomer::TYPE_COMPANIES
            ];
        });

        $individualsMapped = collect($individuals)->map(function ($individual) {
            return [
                'customer_id' => $individual['ID'],
                'name' => "{$individual['GivenName']} {$individual['FamilyName']}",
                'type' => SimproCustomer::TYPE_INDIVIDUALS
            ];
        });

        $customersFromSimpro = $companiesMapped->merge($individualsMapped);

        $simproCustomers = $this->repository->get();

        foreach ($customersFromSimpro as $customerFromSimpro) {
            $simproCustomer = $simproCustomers->first(function ($simproCustomer) use ($customerFromSimpro) {
                return ($simproCustomer['customer_id'] === $customerFromSimpro['customer_id']) && ($simproCustomer['type'] === $customerFromSimpro['type']);
            });

            if ($simproCustomer) {
                if ($simproCustomer['name'] !== $customerFromSimpro['name']) {
                    $this->repository->update($simproCustomer['id'], [
                        'name' => $customerFromSimpro['name']
                    ]);
                }

                $simproCustomers = $simproCustomers->where('id', '!=', $simproCustomer['id']);
            } else {
                $this->repository->create($customerFromSimpro);
            }
        }

        if ($simproCustomers->isNotEmpty()) {
            $ids = $simproCustomers->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }
}
