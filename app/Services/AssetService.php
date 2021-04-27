<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Asset;
use App\Models\Role;
use App\Repositories\AssetRepository;
use Illuminate\Support\Arr;

/**
 * @property AssetRepository $repository
 * @mixin AssetRepository
 */
class AssetService extends BaseService
{
    protected SimproApiClient $simproClient;
    protected SimproSiteService $simproSiteService;
    protected SimproCustomerService $simproCustomerService;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(AssetRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->simproSiteService = app(SimproSiteService::class);
        $this->simproCustomerService = app(SimproCustomerService::class);
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

    public function updateOrCreateBySimpro($webhook)
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $assetId = $webhook['data']['reference']['assetID'];

        $assetFromSimpro = $this->simproClient->getAsset($companyId, $assetId);

        $siteId = $assetFromSimpro['Site']['ID'];

        $simproSite = $this->simproSiteService->getOrCreateBySimpro($companyId, $siteId, null);

        $serviceLevels = $this->simproClient->getAssetServiceLevels($companyId, $siteId, $assetId);

        $asset = $this->createOrUpdate($assetFromSimpro, $simproSite['id'], $simproSite['simpro_customer_id'], Arr::get($serviceLevels, '0.ServiceDate'));

        return $asset;
    }

    public function deleteBySimpro($webhook)
    {
        $assetId = $webhook['data']['reference']['assetID'];

        return $this->repository->delete([
            'asset_id' => $assetId,
        ]);
    }

    protected function createOrUpdate($asset, $simproSiteId, $simproCustomerId, $serviceDate)
    {
        return $this->repository->updateOrCreate([
            'asset_id' => $asset['ID'],
        ], [
            'simpro_site_id' => $simproSiteId,
            'simpro_customer_id' => $simproCustomerId,
            'name' => Arr::get($asset, 'AssetType.Name'),
            'type' => $asset['ParentID'] ? Asset::TYPE_CHILD : Asset::TYPE_PARENT,
            'parent_id' => $asset['ParentID'],
            'last_test_date' => Arr::get($asset, 'LastTest.Date'),
            'next_service_date' => $serviceDate,
            'last_test_result' => Arr::get($asset, 'LastTest.Result'),
            'service_level_name' => Arr::get($asset, 'LastTest.ServiceLevel.Name'),
            'archived' => $asset['Archived']
        ]);
    }
}
