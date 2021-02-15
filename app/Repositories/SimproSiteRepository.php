<?php

namespace App\Repositories;

use App\Models\SimproSite;

/**
 * @property SimproSite $model
*/
class SimproSiteRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(SimproSite::class);
    }
}
