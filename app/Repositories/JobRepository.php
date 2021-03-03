<?php

namespace App\Repositories;

use App\Models\Job;
use Illuminate\Support\Arr;
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
}
