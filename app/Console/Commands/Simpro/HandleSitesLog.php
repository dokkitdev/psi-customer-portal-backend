<?php

namespace App\Console\Commands\Simpro;

use App\Models\SiteLog;
use App\Services\SimproSiteService;
use App\Services\SiteLogService;
use Illuminate\Console\Command;
use Exception;

class HandleSitesLog extends Command
{
    protected $signature = 'sites-log:handle';

    protected $description = 'Handle Sites Log';

    protected SiteLogService $siteLogService;
    protected SimproSiteService $simproSiteService;

    public function handle()
    {
        $this->siteLogService = app(SiteLogService::class);
        $this->simproSiteService = app(SimproSiteService::class);

        $this->siteLogService
            ->getForHandle(1000)
            ->each(function ($siteLog) {
                try {
                    $this->simproSiteService->createOrUpdateBySimpro([
                        'data' => [
                            'reference' => [
                                'companyID' => 0,
                                'siteID' => $siteLog['site_id']
                            ]
                        ]
                    ]);

                    $this->siteLogService->delete($siteLog['id']);
                } catch (Exception $e) {
                    report($e);

                    $this->siteLogService->update($siteLog['id'], [
                        'handle_status' => SiteLog::HANDLE_STATUS_ERROR,
                        'handle_result' => [
                            'code' => $e->getCode(),
                            'message' => $e->getMessage()
                        ]
                    ]);
                }
            });
    }
}
