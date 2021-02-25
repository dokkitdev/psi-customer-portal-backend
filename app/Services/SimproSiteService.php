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
                $simproSite = $this->createOrUpdateBySimpro($site);

                $this->groupSimproSiteService->create([
                    'group_id' => $groupId,
                    'simpro_site_id' => $simproSite['id']
                ]);
            }
        }
    }

    public function getOrCreateBySimpro($companyId, $siteId)
    {
        $simproSite = $this->repository->findBy('site_id', $siteId);

        if (!$simproSite) {
            $site = $this->simproClient->getSite($companyId, $siteId);

            $simproSite = $this->createOrUpdateBySimpro($site);
        }

        return $simproSite;
    }

    protected function createOrUpdateBySimpro($site)
    {
        return $this->repository->updateOrCreate([
            'site_id' => $site['ID']
        ], [
            'name' => $site['Name'],
            'address' => $this->prepareAddress($site),
            'postal_code' => $site['Address']['PostalCode'],
        ]);
    }

    protected function prepareAddress($site)
    {
        $address = [];

        if (!empty($site['Address']['Address'])) {
            $address[] = str_replace(["\r\n", "\n", "\r"], ' ', $site['Address']['Address']);
        }

        if (!empty($site['Address']['City'])) {
            $address[] = $site['Address']['City'];
        }

        if (!empty($site['Address']['State'])) {
            $address[] = $site['Address']['State'];
        }

        $address = trim(implode(', ', $address));

        return $address ? $address : null;
    }
}
