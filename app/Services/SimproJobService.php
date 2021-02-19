<?php

namespace App\Services;

use App\Repositories\SimproJobRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property SimproJobRepository $repository
 * @mixin SimproJobRepository
 */
class SimproJobService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(SimproJobRepository::class);
    }
}
