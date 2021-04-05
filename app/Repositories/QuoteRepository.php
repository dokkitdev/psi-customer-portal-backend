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

    public function checkGroupPermissions($quoteId, $userId)
    {
        return $this->getQuery()
            ->groupPermissions($userId)
            ->find($quoteId);
    }

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query->groupPermissions($this->filter['site_has_user']);
        }

        return $this;
    }
}
