<?php

namespace App\Services;

use App\Repositories\AssetCustomFieldRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property AssetCustomFieldRepository $repository
 * @mixin AssetCustomFieldRepository
 */
class AssetCustomFieldService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(AssetCustomFieldRepository::class);
    }
}
