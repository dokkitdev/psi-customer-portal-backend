<?php

namespace App\Http\Requests\QuoteDeclineReasons;

use App\Http\Requests\Request;

class SearchQuoteDeclineReasonRequest extends Request
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
        ];
    }
}