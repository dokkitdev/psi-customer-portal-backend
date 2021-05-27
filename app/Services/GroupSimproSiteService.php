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

    public function isSiteEnabled($groupId)
    {
        return !$this->repository->exists(['group_id' => $groupId, 'is_enabled' => false]);
    }
}
