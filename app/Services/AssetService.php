<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Repositories\AssetRepository;

/**
 * @property AssetRepository $repository
 * @mixin AssetRepository
 */
class AssetService extends BaseService
{
    protected SimproApiClient $simproClient;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(AssetRepository::class);

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
            ->filterByIntQuery('asset_id')
            ->filterBy('simpro_customer_id')
            ->filterBy('simpro_site_id')
            ->filterByIntQuery('parent_id')
            ->filterBy('type')
            ->filterBy('archived')
            ->filterByQuery(['name'])
            ->filterBy('last_test_date')
            ->filterFrom('last_test_date', false, 'last_test_date_from')
            ->filterTo('last_test_date', false, 'last_test_date_to')
            ->filterBy('next_service_date')
            ->filterFrom('next_service_date', false, 'next_service_date_from')
            ->filterTo('next_service_date', false, 'next_service_date_to')
            ->filterByLastTestResult()
            ->filterByServiceLevelName()
            ->filterByUserGroups()
            ->with()
            ->getSearchResults();
    }
}
