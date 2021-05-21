<?php

namespace App\Services;

use App\Repositories\AssetLogHistoryRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property AssetLogHistoryRepository $repository
 * @mixin AssetLogHistoryRepository
 */
class AssetLogHistoryService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(AssetLogHistoryRepository::class);
    }
}
