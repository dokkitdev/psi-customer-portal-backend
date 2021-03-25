<?php

namespace App\Http\Requests\SimproSites;

use App\Http\Requests\Request;

class SearchSimproSiteRequest extends Request
{
    public function rules()
    {
        $with = 'group_simpro_sites,simpro_customer,site_custom_fields,site_contacts,primary_site_contact,reference_site_custom_field,customer_ref_site_custom_field';

        return [
            'group_id' => 'integer',
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => "string|in:{$with}",
            'with_count' => 'array',
            'with_count.*' => 'string|in:open_jobs'
        ];
    }
}