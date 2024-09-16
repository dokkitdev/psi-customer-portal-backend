<?php

namespace App\Repositories;

use App\Models\Quote;
use Illuminate\Database\Eloquent\Builder;
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

    public function countByStatusAndStage(?int $onlyPermittedForUserId, string $status, string $stage): int
    {
        return $this
            ->getQuery()
            ->when(isset($onlyPermittedForUserId), function (Builder $query) use ($onlyPermittedForUserId) {
                $query->onlyPermitted($onlyPermittedForUserId);
            })
            ->whereHas('quote_status_code', function (Builder $query) use ($status, $stage) {
                $query
                    ->where('quote_status_codes.status', $status)
                    ->where('quote_status_codes.stage', $stage);
            })
            ->count();
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
