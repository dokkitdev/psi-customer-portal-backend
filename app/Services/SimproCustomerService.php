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
        $companiesPages = $this->simproClient->getCustomersAsGenerator($this->companyId, SimproCustomer::TYPE_COMPANIES);
        $individualPages = $this->simproClient->getCustomersAsGenerator($this->companyId, SimproCustomer:: TYPE_INDIVIDUALS);

        $companiesMapped = [];
        foreach ($companiesPages as $companyPage) {
            $companies = array_map(function ($company) {
                return [
                    'customer_id' => $company['ID'],
                    'name' => $company['CompanyName'],
                    'type' => SimproCustomer::TYPE_COMPANIES
                ];
            }, $companyPage);

            $companiesMapped = array_merge($companiesMapped, $companies);
        }

        $individualsMapped = [];
        foreach ($individualPages as $individualPage) {
            $individuals = array_map(function ($individual) {
                return [
                    'customer_id' => $individual['ID'],
                    'name' => "{$individual['GivenName']} {$individual['FamilyName']}",
                    'type' => SimproCustomer::TYPE_INDIVIDUALS
                ];
            }, $individualPage);

            $individualsMapped = array_merge($individualsMapped, $individuals);
        }

        $customersFromSimpro = array_merge($companiesMapped, $individualsMapped);

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
