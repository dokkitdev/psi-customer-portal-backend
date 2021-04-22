<?php

namespace App\Repositories;

use App\Models\SiteLog;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property SiteLog $model
*/
class SiteLogRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(SiteLog::class);
    }
}
