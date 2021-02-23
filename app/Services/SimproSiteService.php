<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\SimproSiteRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property SimproSiteRepository $repository
 * @mixin SimproSiteRepository
 */
class SimproSiteService extends EntityService
{
    protected SimproCustomerService $simproCustomerService;
    protected GroupSimproSiteService $groupSimproSiteService;
    protected SimproApiClient $simproClient;
    protected $companyId;

    public function __construct()
    {
        $this->setRepository(SimproSiteRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->companyId = config('services.simpro.company_id');
        $this->simproCustomerService = app(SimproCustomerService::class);
        $this->groupSimproSiteService = app(GroupSimproSiteService::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterBy('group_simpro_sites.group_id')
            ->filterByQuery(['name'])
            ->with()
            ->getSearchResults();
    }

    public function attachSites($simproCustomerId, $groupId)
    {
        $simproCustomer = $this->simproCustomerService->find($simproCustomerId);

        $sitePages = $this->simproClient->getSitesAsGenerator($this->companyId, $simproCustomer['customer_id']);

        foreach ($sitePages as $sitePage) {
            foreach ($sitePage as $site) {
                $simproSite = $this->repository->updateOrCreate(['site_id' => $site['ID']], ['name' => $site['Name']]);

                $this->groupSimproSiteService->create([
                    'group_id' => $groupId,
                    'simpro_site_id' => $simproSite['id']
                ]);
            }
        }
    }

    public function getOrCreateBySimpro($companyId, $siteId)
    {
        $site = $this->repository->findBy('site_id', $siteId);

        if (!$site) {
            $simproSite = $this->simproClient->getSite($companyId, $siteId);

            $site = $this->createOrUpdateBySimpro($simproSite);
        }

        return $site;
    }

    protected function createOrUpdateBySimpro($simproSite)
    {
        return $this->repository->updateOrCreate([
            'site_id' => $simproSite['ID']
        ], [
            'name' => $simproSite['Name'],
            'address' => $this->prepareAddress($simproSite),
            'postal_code' => $simproSite['Address']['PostalCode'],
        ]);
    }

    protected function prepareAddress($simproSite)
    {
        $address = [];

        if (!empty($simproSite['Address']['Address'])) {
            $address[] = str_replace(["\r\n", "\n", "\r"], ' ', $simproSite['Address']['Address']);
        }

        if (!empty($simproSite['Address']['City'])) {
            $address[] = $simproSite['Address']['City'];
        }

        if (!empty($simproSite['Address']['State'])) {
            $address[] = $simproSite['Address']['State'];
        }

        $address = trim(implode(', ', $address));

        return $address ? $address : null;
    }
}
