<?php

namespace App\Http\Requests\SimproSites;

use App\Http\Requests\Request;

class SearchSimproSiteRequest extends Request
{
    public function rules()
    {
        $with = implode(',', [
            'group_simpro_sites',
            'simpro_customer',
            'site_custom_fields',
            'site_contacts',
            'primary_site_contact',
            'reference_site_custom_field',
            'customer_ref_site_custom_field'
        ]);

        return [
            'site_id' => 'integer',
            'simpro_customer_id' => 'integer',
            'postal_code' => 'string',
            'primary_contact_query' => 'string',
            'reference_query' => 'string',
            'customer_ref_query' => 'string',
            'has_open_jobs' => 'boolean',
            'name_query' => 'string',
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