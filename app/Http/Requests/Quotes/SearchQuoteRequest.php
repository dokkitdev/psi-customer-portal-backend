<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;

class SearchQuoteRequest extends Request
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
            'with.*' => 'string|in:job,simpro_site,simpro_customer',
        ];
    }
}