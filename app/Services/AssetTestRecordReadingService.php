<?php

namespace App\Services;

use App\Repositories\AssetTestRecordReadingRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property AssetTestRecordReadingRepository $repository
 * @mixin AssetTestRecordReadingRepository
 */
class AssetTestRecordReadingService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(AssetTestRecordReadingRepository::class);
    }
}
