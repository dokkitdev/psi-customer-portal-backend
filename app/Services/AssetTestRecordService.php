<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\AssetTestRecordRepository;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\EntityService;

/**
 * @property AssetTestRecordRepository $repository
 * @mixin AssetTestRecordRepository
 */
class AssetTestRecordService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected JobService $jobService;
    protected AssetTestRecordReadingService $assetTestRecordReadingService;

    public function __construct()
    {
        $this->setRepository(AssetTestRecordRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->jobService = app(JobService::class);
        $this->assetTestRecordReadingService = app(AssetTestRecordReadingService::class);
    }

    public function syncByAsset($companyId, $siteIdFromSimpro, $assetIdFromSimpro, $assetId)
    {
        $testHistories = $this->simproClient->getAssetTestHistories($companyId, $siteIdFromSimpro, $assetIdFromSimpro);

        $this->repository->delete(['asset_id' => $assetId]);

        foreach ($testHistories as $testHistory) {
            $job = $this->jobService->getOrCreateBySimpro($companyId, Arr::get($testHistory, 'Job.ID'));

            $testRecord = $this->repository->create([
                'asset_id' => $assetId,
                'job_id' => $job['id'],
                'name' => Arr::get($testHistory, 'TestRecord.Employee.Name'),
                'test_date' => Arr::get($testHistory, 'TestRecord.Date'),
                'notes' => Arr::get($testHistory, 'TestRecord.Notes'),
                'result' => Arr::get($testHistory, 'TestRecord.Result')
            ]);

            foreach ($testHistory['TestReadings'] as $testReading) {
                $this->assetTestRecordReadingService->create([
                    'asset_test_record_id' => $testRecord['id'],
                    'name' => $testReading['Name'],
                    'value' => $testReading['Value'],
                ]);
            }
        }
    }
}
