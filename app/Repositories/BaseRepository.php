<?php

namespace App\Repositories;

class BaseRepository extends \RonasIT\Support\Repositories\BaseRepository
{
    public function get($where = [])
    {
        return $this->getQuery($where)->get();
    }
}