<?php

namespace App\Repositories;

use App\Models\GroupSimproSite;

/**
 * @property GroupSimproSite $model
*/
class GroupSimproSiteRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(GroupSimproSite::class);
    }
}
