<?php

namespace App\Services;

use App\Repositories\GroupSimproSiteRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property GroupSimproSiteRepository $repository
 * @mixin GroupSimproSiteRepository
 */
class GroupSimproSiteService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(GroupSimproSiteRepository::class);
    }
}
