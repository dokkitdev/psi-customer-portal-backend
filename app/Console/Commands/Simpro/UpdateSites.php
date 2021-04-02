<?php

namespace App\Console\Commands\Simpro;

use App\ApiClients\SimproApiClient;
use App\Services\SimproSiteService;
use Exception;
use Illuminate\Console\Command;
use Symfony\Component\HttpFoundation\Response;

class UpdateSites extends Command
{
    protected $signature = 'simpro:update-sites';

    protected $description = 'Update Simpro sites';

    protected SimproSiteService $simproSiteService;
    protected SimproApiClient $simproClient;

    public function handle()
    {
        $this->simproClient = app(SimproApiClient::class);
        $this->simproSiteService = app(SimproSiteService::class);

        $simproSites = $this->simproSiteService->get();

        foreach ($simproSites as $simproSite) {
            try {
                $this->simproClient->getSite(0, $simproSite['site_id']);
            } catch (Exception $e) {
                if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                    $this->simproSiteService->delete($simproSite['id']);
                }

                report($e);

                continue;
            }

            try {
                $this->simproSiteService->createOrUpdateBySimpro([
                    'data' => [
                        'reference' => [
                            'companyID' => 0,
                            'siteID' => $simproSite['site_id']
                        ]
                    ]
                ]);
            } catch (Exception $e) {
                report($e);
            }
        }

        $this->line('Simpro Sites updated');
    }
}
