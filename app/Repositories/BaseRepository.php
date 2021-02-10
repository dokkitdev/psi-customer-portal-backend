<?php

namespace App\Repositories;

use RonasIT\Support\Repositories\BaseRepository as Repository;

class BaseRepository extends Repository
{
    protected $collectionMode = true;

    public function setCollectionMode($value = true)
    {
        $this->collectionMode = $value;

        return $this;
    }

    public function get($where = [])
    {
        $entities = $this->getQuery($where)->get();

        if (!$this->collectionMode) {
            return $entities->toArray();
        }

        return $entities;
    }
}