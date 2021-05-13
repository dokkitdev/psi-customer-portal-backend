<?php

namespace App\Repositories;

use App\Models\Quote;
use Illuminate\Support\Arr;

/**
 * @property Quote $model
*/
class QuoteRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Quote::class);
    }

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query->onlyPermitted($this->filter['site_has_user']);
        }

        return $this;
    }

    public function filterByNote()
    {
        if (Arr::has($this->filter, 'note_query')) {
            $this->query->where($this->getQuerySearchCallbackWithValue('note', $this->filter['note_query']));
        }

        return $this;
    }

    public function filterByName()
    {
        if (Arr::has($this->filter, 'name_query')) {
            $this->query->where($this->getQuerySearchCallbackWithValue('name', $this->filter['name_query']));
        }

        return $this;
    }
}
