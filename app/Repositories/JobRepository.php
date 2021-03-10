<?php

namespace App\Repositories;

use App\Models\Job;
use App\Models\Role;
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

    public function filterByUserGroups($user)
    {
        if ($user['role_id'] === Role::USER) {
            $this->query->whereHas('simpro_site.group_simpro_sites', function ($query) use ($user) {
                $query
                    ->where('is_enabled', true)
                    ->whereHas('group.users', function ($query) use ($user) {
                        $query->where('user_id', $user['id']);
                    });
            });
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
