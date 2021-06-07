<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use RonasIT\Support\Services\EntityService;

/**
 * @property GroupRepository $repository
 * @mixin GroupRepository
 */
class GroupService extends EntityService
{
    protected SimproSiteService $simproSiteService;
    protected GroupSimproSiteService $groupSimproSiteService;

    public function __construct()
    {
        $this->setRepository(GroupRepository::class);

        $this->simproSiteService = app(SimproSiteService::class);
        $this->groupSimproSiteService = app(GroupSimproSiteService::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterBy('simpro_customer_id')
            ->filterByQuery(['title'])
            ->with()
            ->getSearchResults();
    }

    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            $group = $this->repository->create($data);

            $this->simproSiteService->attachSites($group['simpro_customer_id'], $group);

            return $group;
        });
    }

    public function update($where, $data)
    {
        return DB::transaction(function () use ($where, $data) {
            $group = $this->repository->first($where);

            if (Arr::has($data, 'simpro_customer_id')) {
                if ($group['simpro_customer_id'] !== $data['simpro_customer_id']) {
                    $this->groupSimproSiteService->delete(['group_id' => $group['id']]);

                    $this->simproSiteService->attachSites($data['simpro_customer_id'], $group);
                }
            }

            return $this->repository->update($where, $data);
        });
    }

    public function enableSites($where, $data)
    {
        $this->groupSimproSiteService->updateMany(['group_id' => $where], [
            'is_enabled' => $data['is_enabled']
        ]);
    }
}
