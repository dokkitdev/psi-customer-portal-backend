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

    public function findByPermissions($quoteId, $userId)
    {
        return $this->getQuery()
            ->onlyPermitted($userId)
            ->find($quoteId);
    }

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query->onlyPermitted($this->filter['site_has_user']);
        }

        return $this;
    }
}
