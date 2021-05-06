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
            $this->query->onlyPermitted($this->filter['site_has_user']);
        }

        return $this;
    }

    public function filterByPaid()
    {
        if (Arr::has($this->filter, 'is_paid')) {
            if (Arr::get($this->filter, 'is_paid')) {
                $this->query->whereNotNull('date_paid');
            } else {
                $this->query->whereNull('date_paid');
            }
        }

        return $this;
    }
}
