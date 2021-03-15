<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Models\SimproCustomer;
use App\Repositories\SimproCustomerRepository;
use Illuminate\Support\Arr;

/**
 * @property SimproCustomerRepository $repository
 * @mixin SimproCustomerRepository
 */
class SimproCustomerService extends BaseService
{
    protected SimproApiClient $simproClient;
    protected $companyId;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(SimproCustomerRepository::class);

        $this->simproClient = app(SimproApiClient::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function search($filters)
    {
        $authUser = $this->getAuthUser();

        if ($authUser['role_id'] === Role::USER) {
            $filters['customer_has_user'] = $authUser['id'];
        }

        if (Arr::has($filters, 'query') && preg_match('/^\d+$/', $filters['query'])) {
            $filters['customer_id'] = (int) Arr::pull($filters, 'query');
        }

        return $this->repository
            ->searchQuery($filters)
            ->filterBy('customer_id')
            ->filterByQuery(['name'])
            ->hasGroup()
            ->filterByUserGroups()
            ->with()
            ->getSearchResults();
    }

    public function syncCustomers()
    {
        $companiesPages = $this->simproClient->getCustomersAsGenerator($this->companyId, SimproCustomer::TYPE_COMPANIES);
        $individualPages = $this->simproClient->getCustomersAsGenerator($this->companyId, SimproCustomer:: TYPE_INDIVIDUALS);

        $companiesMapped = [];
        foreach ($companiesPages as $companyPage) {
            $companies = array_map(function ($company) {
                return [
                    'customer_id' => $company['ID'],
                    'name' => $this->getName($company, SimproCustomer::TYPE_COMPANIES),
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
                    'name' => $this->getName($individual, SimproCustomer::TYPE_INDIVIDUALS),
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

    public function getOrCreateBySimpro($companyId, $customer)
    {
        $customerId = $customer['ID'];

        $type = (empty($customer['CompanyName'])) ? SimproCustomer::TYPE_INDIVIDUALS : SimproCustomer::TYPE_COMPANIES;

        $simproCustomer = $this->repository->first(['customer_id' => $customerId, 'type' => $type]);

        if (!$simproCustomer) {
            $customer = $this->simproClient->getCustomer($companyId, $type, $customerId);

            $simproCustomer = $this->repository->create([
                'customer_id' => $customerId,
                'type' => $type,
                'name' => $this->getName($customer, $type)
            ]);
        }

        return $simproCustomer;
    }

    public function createOrUpdateBySimpro($webhook, $type)
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $customerId = $this->getCustomerId($webhook);

        $customer = $this->simproClient->getCustomer($companyId, $type, $customerId);

        $this->repository->updateOrCreate([
            'customer_id' => $customerId,
            'type' => $type
        ], [
            'name' => $this->getName($customer, $type)
        ]);
    }

    public function deleteBySimpro($webhook, $type)
    {
        $customerId = $this->getCustomerId($webhook);

        $this->repository->delete([
            'customer_id' => $customerId,
            'type' => $type
        ]);
    }

    protected function getName($customer, $type)
    {
        if ($type === SimproCustomer::TYPE_INDIVIDUALS) {
            return "{$customer['GivenName']} {$customer['FamilyName']}";
        }

        return $customer['CompanyName'];
    }

    protected function getCustomerId($webhook)
    {
        preg_match('/(\d+)/', $webhook['data']['description'], $matches);

        return $matches[0];
    }
}
