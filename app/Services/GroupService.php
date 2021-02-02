<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property GroupRepository $repository
 * @mixin GroupRepository
 */
class GroupService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(GroupRepository::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterByQuery(['title'])
            ->with()
            ->getSearchResults();
    }
}
