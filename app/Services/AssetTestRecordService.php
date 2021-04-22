<?php

namespace App\Services;

use App\Repositories\AssetTestRecordRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property AssetTestRecordRepository $repository
 * @mixin AssetTestRecordRepository
 */
class AssetTestRecordService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(AssetTestRecordRepository::class);
    }
}
