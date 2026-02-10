<?php

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * @property Invoice $model
*/
class InvoiceRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Invoice::class);
    }

    public function countNotPaid(?int $onlyPermittedForUserId): int
    {
        return $this
            ->getQuery()
            ->when(isset($onlyPermittedForUserId), function (Builder $query) use ($onlyPermittedForUserId) {
                $query->onlyPermitted($onlyPermittedForUserId);
            })
            ->where('is_paid', false)
            ->count();
    }

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query->onlyPermitted($this->filter['site_has_user']);
        }

        return $this;
    }
}
