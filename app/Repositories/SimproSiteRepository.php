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

    public function findByPermissions($siteId, $userId)
    {
        return $this->getQuery()
            ->onlyPermitted($userId)
            ->find($siteId);
    }

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query->onlyPermitted($this->filter['site_has_user']);
        }

        return $this;
    }
}
