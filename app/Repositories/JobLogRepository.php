<?php

namespace App\Repositories;

use App\Models\JobLog;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property JobLog $model
*/
class JobLogRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(JobLog::class);
    }

    public function getForHandle($limit = 100)
    {
        return $this
            ->getQuery(['handle_status' => JobLog::HANDLE_STATUS_NEW])
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();
    }
}
