<?php

namespace App\Repositories;

use Illuminate\Support\Arr;
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

    public function update($where, array $data)
    {
        $item = $this->getQuery($where)->first();

        if (empty($item)) {
            return [];
        }

        if ($this->forceMode) {
            $item->forceFill(Arr::only($data, $this->fields));
        } else {
            $item->fill(Arr::only($data, $item->getFillable()));
        }

        $item->save();
        $item->refresh();

        $this->afterUpdateHook($item, $data);

        if (!empty($this->requiredRelations)) {
            $item->load($this->requiredRelations);
        }

        if (!$this->collectionMode) {
            return $item->toArray();
        }

        return $item;
    }

    public function create($data)
    {
        $entityData = Arr::only($data, $this->fields);
        $modelClass = get_class($this->model);
        $model = new $modelClass();

        if ($this->forceMode) {
            $model->forceFill($entityData);
        } else {
            $model->fill(Arr::only($entityData, $model->getFillable()));
        }

        $model->save();
        $model->refresh();

        $this->afterCreateHook($model, $data);

        if (!empty($this->requiredRelations)) {
            $model->load($this->requiredRelations);
        }

        if (!$this->collectionMode) {
            return $model->toArray();
        }

        return $model;
    }
}