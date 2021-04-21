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

    public function filterByNote()
    {
        if (Arr::has($this->filter, 'note_query')) {
            $this->query->where($this->getQuerySearchCallbackWithValue('note', $this->filter['note_query']));
        }

        return $this;
    }
}
