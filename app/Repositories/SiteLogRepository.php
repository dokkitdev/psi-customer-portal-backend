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

    public function getForHandle($limit = 100)
    {
        return $this
            ->getQuery(['handle_status' => SiteLog::HANDLE_STATUS_NEW])
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();
    }
}
