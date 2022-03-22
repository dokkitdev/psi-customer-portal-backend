<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Models\User;

class SearchQuoteRequest extends Request
{
    public function authorize()
    {
        return in_array($this->user()->quote_permission_level, [
            User::QUOTE_PERMISSION_LEVEL_VIEW,
            User::QUOTE_PERMISSION_LEVEL_EDIT
        ]);
    }

    public function rules()
    {
        return [
            'quote_id' => 'integer',
            'job_id' => 'integer',
            'simpro_job_id' => 'integer',
            'simpro_customer_id' => 'integer',
            'simpro_site_id' => 'integer',
            'stages' => 'array',
            'stages.*' => 'string',
            'statuses' => 'array',
            'statuses.*' => 'string',
            'cost_center_names' => 'array',
            'cost_center_names.*' => 'string',
            'business_groups' => 'array',
            'business_groups.*' => 'string',
            'value' => 'numeric',
            'value_from' => 'numeric',
            'value_to' => 'numeric',
            'date_issued' => 'date',
            'date_issued_from' => 'date',
            'date_issued_to' => 'date',
            'date_expiry' => 'date',
            'date_expiry_from' => 'date',
            'date_expiry_to' => 'date',
            'note_query' => 'string',
            'name_query' => 'string',
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => 'string|in:job,simpro_site,simpro_customer,quote_status_code',
        ];
    }
}