<?php

namespace App\Http\Requests\Jobs;

use App\Http\Requests\Request;

class SearchJobRequest extends Request
{
    public function rules()
    {
        return [
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => 'string|in:simpro_site,simpro_customer'
        ];
    }
}