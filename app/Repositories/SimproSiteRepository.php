<?php

namespace App\Repositories;

use App\Models\SimproSite;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @property SimproSite $model
*/
class SimproSiteRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(SimproSite::class);
    }

    public function filterByUserGroups()
    {
        if (Arr::has($this->filter, 'site_has_user')) {
            $this->query->onlyPermitted($this->filter['site_has_user']);
        }

        return $this;
    }

    public function filterByPostalCode()
    {
        if (Arr::has($this->filter, 'postal_code')) {
            $postalCode = str_replace(' ', '', $this->filter['postal_code']);

            $this->query->where(DB::raw("REPLACE(postal_code, ' ', '')"), $postalCode);
        }

        return $this;
    }

    public function filterByPrimaryContact()
    {
        if (Arr::has($this->filter, 'primary_contact_query')) {
            $this->query->whereHas('primary_site_contact', function ($query) {
                $query->where($this->getQuerySearchCallbackWithValue('name', $this->filter['primary_contact_query']));
            });
        }

        return $this;
    }

    public function filterByReference()
    {
        if (Arr::has($this->filter, 'reference_query')) {
            $this->query->whereHas('reference_site_custom_field', function ($query) {
                $query->where($this->getQuerySearchCallbackWithValue('value', $this->filter['reference_query']));
            });
        }

        return $this;
    }

    public function filterByCustomerRef()
    {
        if (Arr::has($this->filter, 'customer_ref_query')) {
            $this->query->whereHas('customer_ref_site_custom_field', function ($query) {
                $query->where($this->getQuerySearchCallbackWithValue('value', $this->filter['customer_ref_query']));
            });
        }

        return $this;
    }

    public function filterByOpenJobs()
    {
        if (Arr::has($this->filter, 'has_open_jobs')) {
            if (Arr::get($this->filter, 'has_open_jobs')) {
                $this->query->whereHas('open_jobs');
            } else {
                $this->query->whereDoesntHave('open_jobs');
            }
        }

        return $this;
    }
}
