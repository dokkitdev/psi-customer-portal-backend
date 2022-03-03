<?php

namespace App\Http\Requests\QuoteStatusCodes;

use App\Http\Requests\Request;

class SearchQuoteStatusCodeRequest extends Request
{
    public function rules()
    {
        return [
            'status' => 'string|nullable',
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
        ];
    }
}