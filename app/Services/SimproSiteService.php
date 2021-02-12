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
}
