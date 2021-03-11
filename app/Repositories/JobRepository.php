<?php

namespace App\Repositories;

use App\Models\Job;
use Illuminate\Support\Arr;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property Job $model
*/
class JobRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Job::class);
    }

    public function checkGroupPermissions($jobId, $userId)
    {
        return $this->getQuery()
            ->groupPermissions($userId)
            ->find($jobId);
    }

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query->groupPermissions($this->filter['site_has_user']);
        }

        return $this;
    }

    public function filterByRequested()
    {
        if (Arr::has($this->filter, 'requested')) {
            if (Arr::get($this->filter, 'requested')) {
                $this->query->whereNotNull('requested');
            } else {
                $this->query->whereNull('requested');
            }
        }

        return $this;
    }
}
