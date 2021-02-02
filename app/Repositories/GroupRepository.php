<?php

namespace App\Repositories;

use App\Models\Group;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property Group $model
*/
class GroupRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Group::class);
    }
}
