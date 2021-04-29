<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\AssetLog;
use App\Repositories\AssetLogRepository;
use Exception;
use RonasIT\Support\Services\EntityService;

/**
 * @property AssetLogRepository $repository
 * @mixin AssetLogRepository
 */
class AssetLogService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected AssetService $assetService;
    protected $companyId;

    public function __construct()
    {
        $this->setRepository(AssetLogRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->assetService = app(AssetService::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterBy('handle_status')
            ->getSearchResults();
    }

    public function saveAllAssets()
    {
        $assetsPages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/customerAssets/");

        foreach ($assetsPages as $assetsPage) {
            foreach ($assetsPage as $assetFromSimpro) {
                $this->repository->updateOrCreate(['asset_id' => $assetFromSimpro['ID']], []);
            }
        }
    }

    public function handleLog()
    {
        $assetLogs = $this->search([
            'handle_status' => AssetLog::HANDLE_STATUS_NEW,
            'page' => 1,
            'per_page' => 1000,
            'order_by' => 'id',
        ]);

        foreach ($assetLogs['data'] as $assetLog) {
            try {
                $this->assetService->updateOrCreateBySimpro([
                    'data' => [
                        'reference' => [
                            'companyID' => 0,
                            'assetID' => $assetLog['asset_id']
                        ],
                    ]
                ]);

                $this->delete($assetLog['id']);
            } catch (Exception $e) {
                report($e);

                $this->update($assetLog['id'], [
                    'handle_status' => AssetLog::HANDLE_STATUS_ERROR,
                    'handle_result' => [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage()
                    ]
                ]);
            }
        }
    }
}
