<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\AssetLog;
use App\Repositories\AssetLogRepository;
use Carbon\Carbon;
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
    protected AssetLogDateService $assetLogDateService;

    public function __construct()
    {
        $this->setRepository(AssetLogRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->assetService = app(AssetService::class);
        $this->assetLogDateService = app(AssetLogDateService::class);

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
        $startDate = now()->subMinutes(30);

        $assetLogDate = $this->assetLogDateService->last();

        $headers = [];

        if ($assetLogDate) {
            $headers['If-Modified-Since'] = Carbon::createFromFormat('Y-m-d H:i:s', $assetLogDate['last_date'])->toRfc7231String();
        }

        $assetsPages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/customerAssets/", [], null, 250, $headers);

        foreach ($assetsPages as $assetsPage) {
            foreach ($assetsPage as $assetFromSimpro) {
                $this->repository->updateOrCreate(['asset_id' => $assetFromSimpro['ID']], []);
            }
        }

        if ($assetLogDate) {
            $this->assetLogDateService->update($assetLogDate['id'], [
                'last_date' => $startDate
            ]);
        } else {
            $this->assetLogDateService->create([
                'last_date' => $startDate
            ]);
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
                        ],
                        'description' => "{$assetLog['asset_id']}"
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
