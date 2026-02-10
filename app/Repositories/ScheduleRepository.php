<?php

namespace App\Repositories;

use App\Models\Schedule;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property Schedule $model
*/
class ScheduleRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Schedule::class);
    }

    public function getRecentSchedule($jobId)
    {
        return $this
            ->getQuery(['job_id' => $jobId])
            ->orderBy('date', 'desc')
            ->first();
    }
}
