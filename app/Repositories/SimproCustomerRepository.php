<?php

namespace App\Repositories;

use App\Models\SimproCustomer;

/**
 * @property SimproCustomer $model
*/
class SimproCustomerRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(SimproCustomer::class);
    }
}
