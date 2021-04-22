<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\SiteLog;
use App\Repositories\SiteLogRepository;
use Exception;
use RonasIT\Support\Services\EntityService;

/**
 * @property SiteLogRepository $repository
 * @mixin SiteLogRepository
 */
class SiteLogService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected $companyId;
    protected SimproSiteService $simproSiteService;

    public function __construct()
    {
        $this->setRepository(SiteLogRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->simproSiteService = app(SimproSiteService::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterBy('handle_status')
            ->getSearchResults();
    }

    public function saveAllSites()
    {
        $sitePages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/sites/");

        foreach ($sitePages as $sitePage) {
            foreach ($sitePage as $siteFromSimpro) {
                $this->repository->updateOrCreate(['site_id' => $siteFromSimpro['ID']], []);
            }
        }
    }

    public function handleLog()
    {
        $siteLogs = $this->search([
            'handle_status' => SiteLog::HANDLE_STATUS_NEW,
            'page' => 1,
            'per_page' => 1000,
            'order_by' => 'id',
        ]);

        foreach ($siteLogs['data'] as $siteLog) {
            try {
                $this->simproSiteService->createOrUpdateBySimpro([
                    'data' => [
                        'reference' => [
                            'companyID' => 0,
                            'siteID' => $siteLog['site_id']
                        ]
                    ]
                ]);

                $this->delete($siteLog['id']);
            } catch (Exception $e) {
                report($e);

                $this->update($siteLog['id'], [
                    'handle_status' => SiteLog::HANDLE_STATUS_ERROR,
                    'handle_result' => [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage()
                    ]
                ]);
            }
        }
    }
}
