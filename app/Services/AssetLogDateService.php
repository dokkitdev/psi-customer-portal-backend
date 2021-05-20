<?php

namespace App\Services;

use App\Repositories\AssetLogDateRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property AssetLogDateRepository $repository
 * @mixin AssetLogDateRepository
 */
class AssetLogDateService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(AssetLogDateRepository::class);
    }
}
