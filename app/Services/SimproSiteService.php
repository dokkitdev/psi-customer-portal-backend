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
            ->filterBy('group_simpro_sites.group_id')
            ->filterByQuery(['name'])
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

    public function attachSites($simproCustomerId, $groupId)
    {
        $simproCustomer = $this->simproCustomerService->find($simproCustomerId);

        $sitePages = $this->simproClient->getSitesAsGenerator($this->companyId, $simproCustomer['customer_id']);

        foreach ($sitePages as $sitePage) {
            foreach ($sitePage as $site) {
                $simproSite = $this->createOrUpdate($site, $simproCustomerId);

                $this->groupSimproSiteService->firstOrCreate([
                    'group_id' => $groupId,
                    'simpro_site_id' => $simproSite['id']
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

            $this->createGroupSimproSites($simproCustomerId, $simproSite['id']);
        }

        return $simproSite;
    }

    public function createOrUpdateBySimpro($webhook)
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $siteIdFromSimpro = $webhook['data']['reference']['siteID'];

        $siteFromSimpro = $this->simproClient->getSite($companyId, $siteIdFromSimpro);

        $simproCustomer = $this->simproCustomerService->getOrCreateBySimpro($companyId, Arr::first($siteFromSimpro['Customers']));

        $simproSite = $this->createOrUpdate($siteFromSimpro, $simproCustomer['id']);

        $this->siteCustomFieldService->createOrUpdateBySite($siteFromSimpro, $simproSite['id']);

        $this->siteContactService->syncBySite($companyId, $siteIdFromSimpro, $simproSite['id']);

        $this->createGroupSimproSites($simproCustomer['id'], $simproSite['id']);

        return $simproSite;
    }

    public function deleteBySimpro($webhook)
    {
        $siteIdFromSimpro = $webhook['data']['reference']['siteID'];

        return $this->repository->delete([
            'site_id' => $siteIdFromSimpro,
        ]);
    }

    protected function createOrUpdate($site, $simproCustomerId)
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
                    'simpro_site_id' => $simproSiteId
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
