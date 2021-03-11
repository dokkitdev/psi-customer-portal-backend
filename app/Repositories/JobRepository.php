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

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query
                ->whereHas('simpro_customer.groups.users', function ($query) {
                    $query->where('user_id', $this->filter['site_has_user']);
                })
                ->whereHas('simpro_site.group_simpro_sites', function ($query) {
                    $query
                        ->where('is_enabled', true)
                        ->whereHas('group.users', function ($query) {
                            $query->where('user_id', $this->filter['site_has_user']);
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
