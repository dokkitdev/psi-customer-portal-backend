<?php

namespace App\Repositories;

use App\Models\SimproCustomer;
use Illuminate\Support\Arr;

/**
 * @property SimproCustomer $model
*/
class SimproCustomerRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(SimproCustomer::class);
    }

    public function hasGroup()
    {
        if (Arr::has($this->filter, 'has_groups')) {
            if ($this->filter['has_groups']) {
                $this->query->whereHas('groups');
            } else {
                $this->query->whereDoesntHave('groups');
            }
        }

        return $this;
    }
}
