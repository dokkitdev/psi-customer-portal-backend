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

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'customer_has_user')) {
            $this->query->whereHas('groups.users', function ($query) {
                $query->where('user_id', $this->filter['customer_has_user']);
            });
        }

        return $this;
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

    public function filterByNameOrId()
    {
        if (Arr::has($this->filter, 'query')) {
            $this->query->where(function ($query) {
                $query->where($this->getQuerySearchCallback('name'));

                if (preg_match('/^\d+/', $this->filter['query'])) {
                    $customerId = (int) $this->filter['query'];

                    $query->orWhere('customer_id', $customerId);
                }
            });
        }

        return $this;
    }
}
