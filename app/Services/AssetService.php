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
    protected AssetCustomFieldService $assetCustomFieldService;
    protected AssetAttachmentService $assetAttachmentService;
    protected AssetTestRecordService $assetTestRecordService;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(AssetRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->simproSiteService = app(SimproSiteService::class);
        $this->assetCustomFieldService = app(AssetCustomFieldService::class);
        $this->assetAttachmentService = app(AssetAttachmentService::class);
        $this->assetTestRecordService = app(AssetTestRecordService::class);
    }

    public function search($filters)
    {
        $authUser = $this->getAuthUser();

        if ($authUser && ($authUser['role_id'] === Role::USER)) {
            $filters['site_has_user'] = $authUser['id'];
        }

        return $this->repository
            ->searchQuery($filters)
            ->filterByIntQuery('asset_id')
            ->filterBy('asset_test_records.job_id')
            ->filterBy('simpro_site.simpro_customer_id')
            ->filterBy('simpro_site_id')
            ->filterByIntQuery('parent_id')
            ->filterBy('type')
            ->filterBy('archived')
            ->filterByList('service_level_name', 'service_level_names')
            ->filterByQuery(['name'])
            ->filterBy('last_test_date')
            ->filterFrom('last_test_date', false, 'last_test_date_from')
            ->filterTo('last_test_date', false, 'last_test_date_to')
            ->filterBy('next_service_date')
            ->filterFrom('next_service_date', false, 'next_service_date_from')
            ->filterTo('next_service_date', false, 'next_service_date_to')
            ->filterByLastTestResult()
            ->filterByUserGroups()
            ->with()
            ->getSearchResults();
    }

    public function updateOrCreateBySimpro($webhook)
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $assetId = $this->getAssetId($webhook);

        return $this->createOrUpdateAsset($companyId, $assetId);
    }

    public function updateByJob($companyId, $jobId)
    {
        $assets = $this->search(['job_id' => $jobId]);

        foreach ($assets['data'] as $asset) {
            $this->createOrUpdateAsset($companyId, $asset['asset_id']);
        }
    }

    public function deleteBySimpro($webhook)
    {
        $assetId = $this->getAssetId($webhook);

        return $this->repository->delete([
            'asset_id' => $assetId,
        ]);
    }

    protected function createOrUpdateAsset($companyId, $assetId)
    {
        $assetFromSimpro = $this->simproClient->getAsset($companyId, $assetId);

        $siteId = $assetFromSimpro['Site']['ID'];

        $simproSite = $this->simproSiteService->getOrCreateBySimpro($companyId, $siteId, null);

        $serviceLevels = $this->simproClient->getAssetServiceLevels($companyId, $siteId, $assetId);

        $asset = $this->createOrUpdate($assetFromSimpro, $simproSite['id'], Arr::get($serviceLevels, '0.ServiceDate'));

        $this->assetCustomFieldService->syncByAsset($assetFromSimpro, $asset['id']);

        $this->assetAttachmentService->syncByAsset($companyId, $siteId, $assetId, $asset['id']);

        $this->assetTestRecordService->syncByAsset($companyId, $siteId, $assetId, $asset['id']);

        return $asset;
    }

    protected function createOrUpdate($asset, $simproSiteId, $serviceDate)
    {
        return $this->repository->updateOrCreate([
            'asset_id' => $asset['ID'],
        ], [
            'simpro_site_id' => $simproSiteId,
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

    protected function getAssetId($webhook)
    {
        preg_match('/(\d+)/', $webhook['data']['description'], $matches);

        return $matches[0];
    }
}
