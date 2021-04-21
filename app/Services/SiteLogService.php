<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\SiteLogRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property SiteLogRepository $repository
 * @mixin SiteLogRepository
 */
class SiteLogService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected $companyId;

    public function __construct()
    {
        $this->setRepository(SiteLogRepository::class);

        $this->simproClient = app(SimproApiClient::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function saveAllSites()
    {
        $sitePages = $this->simproClient->getSitesAsGenerator($this->companyId);

        foreach ($sitePages as $sitePage) {
            foreach ($sitePage as $siteFromSimpro) {
                $this->createOrUpdate($siteFromSimpro);
            }
        }
    }

    protected function createOrUpdate($site)
    {
        return $this->repository->updateOrCreate(['site_id' => $site['ID']], []);
    }
}
