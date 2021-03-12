<?php

namespace App\Repositories;

use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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

    public function checkGroupPermissions($jobId, $userId)
    {
        return $this->getQuery()
            ->groupPermissions($userId)
            ->find($jobId);
    }

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query->groupPermissions($this->filter['site_has_user']);
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

    public function filterByRecentSchedule()
    {
        if (Arr::has($this->filter, 'appointment_from') || Arr::has($this->filter, 'appointment_to') ||
            Arr::has($this->filter, 'start_time_from') || Arr::has($this->filter, 'start_time_to') ||
            Arr::has($this->filter, 'end_time_from') || Arr::has($this->filter, 'end_time_to')) {

            $this->query->whereHas('recent_schedule', function ($query) {
                if (Arr::has($this->filter, 'appointment_from')) {
                    $query->where('date', '>=', $this->filter['appointment_from']);
                }
                if (Arr::has($this->filter, 'appointment_to')) {
                    $query->where('date', '<=', $this->filter['appointment_to']);
                }
                if (Arr::has($this->filter, 'start_time_from')) {
                    $time = Carbon::createFromFormat('Y-m-d H:i:s', $this->filter['start_time_from'])->format('H:i');
                    $query->where(DB::raw("cast(start_time as time)"), '>=', $time);
                }
                if (Arr::has($this->filter, 'start_time_to')) {
                    $time = Carbon::createFromFormat('Y-m-d H:i:s', $this->filter['start_time_to'])->format('H:i');
                    $query->where(DB::raw("cast(start_time as time)"), '<=', $time);
                }
                if (Arr::has($this->filter, 'end_time_from')) {
                    $time = Carbon::createFromFormat('Y-m-d H:i:s', $this->filter['end_time_from'])->format('H:i');
                    $query->where(DB::raw('cast(end_time as time)'), '>=', $time);
                }
                if (Arr::has($this->filter, 'end_time_to')) {
                    $time = Carbon::createFromFormat('Y-m-d H:i:s', $this->filter['end_time_to'])->format('H:i');
                    $query->where(DB::raw('cast(end_time as time)'), '<=', $time);
                }
            });
        }

        return $this;
    }
}
