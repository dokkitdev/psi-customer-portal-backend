<?php

namespace App\Repositories;

use App\Models\Invoice;
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

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query->groupPermissions($this->filter['site_has_user']);
        }

        return $this;
    }
}
