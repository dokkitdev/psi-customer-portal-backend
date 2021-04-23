<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Repositories\SimproSiteRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @property SimproSiteRepository $repository
 * @mixin SimproSiteRepository
 */
class SimproSiteService extends BaseService
{
    protected SimproCustomerService $simproCustomerService;
    protected GroupSimproSiteService $groupSimproSiteService;
    protected SimproApiClient $simproClient;
    protected $companyId;
    protected SiteCustomFieldService $siteCustomFieldService;
    protected SiteContactService $siteContactService;
    protected GroupService $groupService;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(SimproSiteRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->companyId = config('services.simpro.company_id');
        $this->simproCustomerService = app(SimproCustomerService::class);
        $this->groupSimproSiteService = app(GroupSimproSiteService::class);
        $this->siteCustomFieldService = app(SiteCustomFieldService::class);
        $this->siteContactService = app(SiteContactService::class);
    }

    public function search($filters)
    {
        $authUser = $this->getAuthUser();

        if ($authUser['role_id'] === Role::USER) {
            $filters['site_has_user'] = $authUser['id'];
        }

        return $this->repository
            ->searchQuery($filters)
            ->filterByIntQuery('site_id')
            ->filterBy('simpro_customer_id')
            ->filterBy('group_simpro_sites.group_id')
            ->filterByQuery(['city', 'county', 'address'])
            ->filterByName()
            ->filterByPostalCode()
            ->filterByPrimaryContact()
            ->filterByReference()
            ->filterByCustomerRef()
            ->filterByOpenJobs()
            ->filterByUserGroups()
            ->with()
            ->withCount()
            ->getSearchResults();
    }

    public function update($where, $data)
    {
        return DB::transaction(function () use ($where, $data) {
            $simproSite = $this->repository->update($where, $data);

            $siteData = $this->prepareSiteData($data);

            $this->simproClient->patchSite($this->companyId, $simproSite['site_id'], $siteData);

            if (Arr::has($data, 'primary_site_contact_id')) {
                $this->siteContactService->setPrimary($data['primary_site_contact_id']);
            }

            if (Arr::has($data, 'site_custom_fields')) {
                foreach ($data['site_custom_fields'] as $customField) {
                    $siteCustomField = $this->siteCustomFieldService->update($customField['id'], [
                        'value' => Arr::get($customField, 'value')
                    ]);

                    $this->simproClient->patchSiteCustomField($this->companyId, $simproSite['site_id'], $siteCustomField['custom_field_id'], [
                        'Value' => Arr::get($customField, 'value')
                    ]);
                }
            }

            return $simproSite;
        });
    }

    public function attachSites($simproCustomerId, $group)
    {
        $simproCustomer = $this->simproCustomerService->find($simproCustomerId);

        $sitePages = $this->simproClient->getAsGenerator(
            "companies/{$this->companyId}/sites/",
            [
                'Customers.ID' => $simproCustomer['customer_id'],
                'columns' => 'ID,Name,Address'
            ]
        );

        foreach ($sitePages as $sitePage) {
            foreach ($sitePage as $site) {
                $simproSite = $this->createOrUpdate($site, $simproCustomerId);

                $this->siteCustomFieldService->createOrUpdateBySite($site, $simproSite['id']);

                $this->siteContactService->syncBySite($this->companyId, $site['ID'], $simproSite['id']);

                $this->groupSimproSiteService->firstOrCreate([
                    'group_id' => $group['id'],
                    'simpro_site_id' => $simproSite['id']
                ], [
                    'is_enabled' => $group['is_enabled_all_sites']
                ]);
            }
        }
    }

    public function getOrCreateBySimpro($companyId, $siteId, $simproCustomerId)
    {
        $simproSite = $this->repository->findBy('site_id', $siteId);

        if (!$simproSite) {
            $site = $this->simproClient->getSite($companyId, $siteId);

            $simproSite = $this->createOrUpdate($site, $simproCustomerId);

            $this->siteCustomFieldService->createOrUpdateBySite($site, $simproSite['id']);

            $this->siteContactService->syncBySite($companyId, $siteId, $simproSite['id']);

            $this->createGroupSimproSites($simproCustomerId, $simproSite['id']);
        }

        return $simproSite;
    }

    public function createOrUpdateBySimpro($webhook)
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $siteIdFromSimpro = $webhook['data']['reference']['siteID'];

        $siteFromSimpro = $this->simproClient->getSite($companyId, $siteIdFromSimpro);

        $siteCustomer = Arr::first($siteFromSimpro['Customers']);

        $simproCustomerId = null;

        if ($siteCustomer) {
            $simproCustomer = $this->simproCustomerService->getOrCreateBySimpro($companyId, $siteCustomer);
            $simproCustomerId = $simproCustomer['id'];
        }

        $simproSite = $this->createOrUpdate($siteFromSimpro, $simproCustomerId);

        $this->siteCustomFieldService->createOrUpdateBySite($siteFromSimpro, $simproSite['id']);

        $this->siteContactService->syncBySite($companyId, $siteIdFromSimpro, $simproSite['id']);

        if ($simproCustomerId) {
            $this->createGroupSimproSites($simproCustomerId, $simproSite['id']);
        }

        return $simproSite;
    }

    public function deleteBySimpro($webhook)
    {
        $siteIdFromSimpro = $webhook['data']['reference']['siteID'];

        return $this->repository->delete([
            'site_id' => $siteIdFromSimpro,
        ]);
    }

    protected function createOrUpdate($site, $simproCustomerId = null)
    {
        return $this->repository->updateOrCreate([
            'site_id' => $site['ID']
        ], [
            'name' => $site['Name'],
            'address' => $site['Address']['Address'],
            'postal_code' => $site['Address']['PostalCode'],
            'simpro_customer_id' => $simproCustomerId,
            'city' => $site['Address']['City'],
            'country' => $site['Address']['Country'],
            'county' => $site['Address']['State'],
        ]);
    }

    protected function createGroupSimproSites($simproCustomerId, $simproSiteId)
    {
        $this->groupService = app(GroupService::class);

        $groups = $this->groupService->get(['simpro_customer_id' => $simproCustomerId]);

        foreach ($groups as $group) {
            if (!$this->groupSimproSiteService->exists(['group_id' => $group['id'], 'simpro_site_id' => $simproSiteId])) {
                $this->groupSimproSiteService->create([
                    'group_id' => $group['id'],
                    'simpro_site_id' => $simproSiteId,
                    'is_enabled' => $group['is_enabled_all_sites']
                ]);
            }
        }
    }

    protected function prepareSiteData($data)
    {
        $siteData = [];

        if (Arr::has($data, 'name')) {
            $siteData['Name'] = $data['name'];
        }
        if (Arr::has($data, 'address')) {
            $siteData['Address']['Address'] = $data['address'];
        }
        if (Arr::has($data, 'postal_code')) {
            $siteData['Address']['PostalCode'] = $data['postal_code'];
        }
        if (Arr::has($data, 'city')) {
            $siteData['Address']['City'] = $data['city'];
        }
        if (Arr::has($data, 'country')) {
            $siteData['Address']['Country'] = $data['country'];
        }
        if (Arr::has($data, 'county')) {
            $siteData['Address']['State'] = $data['county'];
        }

        return $siteData;
    }
}
