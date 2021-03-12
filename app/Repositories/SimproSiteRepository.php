<?php

namespace App\Repositories;

use App\Models\SimproSite;
use Illuminate\Support\Arr;

/**
 * @property SimproSite $model
*/
class SimproSiteRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(SimproSite::class);
    }

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query->whereHas('group_simpro_sites', function ($query) {
                $query
                    ->where('is_enabled', true)
                    ->whereHas('group.users', function ($query) {
                        $query->where('user_id', $this->filter['site_has_user']);
                    });
            });
        }

        return $this;
    }
}
