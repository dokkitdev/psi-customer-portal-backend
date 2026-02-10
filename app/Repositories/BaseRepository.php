<?php

namespace App\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    public function findByPermissions($id, $userId)
    {
        return $this->getQuery()
            ->onlyPermitted($userId)
            ->find($id);
    }

    public function filterByIntQuery($field, $filterName = null)
    {
        if (empty($filterName)) {
            if (Str::contains($field, '.')) {
                $entities = explode('.', $field);
                $filterName = Arr::last($entities);
            } else {
                $filterName = $field;
            }
        }

        if (Arr::has($this->filter, $filterName)) {
            $this->addIntQueryWhere($this->query, $field, $this->filter[$filterName]);
        }

        return $this;
    }

    public function filterByQueryWithValue($field, $filterName)
    {
        if (Arr::has($this->filter, $filterName)) {
            $this->query->where($this->getQuerySearchCallbackWithValue($field, $this->filter[$filterName]));
        }

        return $this;
    }

    protected function addIntQueryWhere(&$query, $field, $value)
    {
        $this->applyWhereCallback($query, $field, function (&$q, $field) use ($value) {
            $q->where($this->getIntQuerySearchCallbackWithValue($field, $value));
        });
    }

    protected function getIntQuerySearchCallbackWithValue($field, $value)
    {
        return function ($query) use ($field, $value) {
            $field = DB::raw("cast({$field} as text)");

            $query->where($field, 'like', "{$value}%");
        };
    }

    protected function getQuerySearchCallbackWithValue($field, $value, $left = '%', $right = '%')
    {
        return function ($query) use ($field, $value, $left, $right) {
            $loweredQuery = mb_strtolower($value);
            $field = DB::raw("lower({$field})");

            $query->orWhere($field, 'like', "{$left}{$loweredQuery}{$right}");
        };
    }
}