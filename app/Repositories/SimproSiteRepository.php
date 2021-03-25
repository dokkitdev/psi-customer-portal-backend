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

    public function checkGroupPermissions($sitedId, $userId)
    {
        return $this->getQuery()
            ->groupPermissions($userId)
            ->find($sitedId);
    }

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query->groupPermissions($this->filter['site_has_user']);
        }

        return $this;
    }
}
