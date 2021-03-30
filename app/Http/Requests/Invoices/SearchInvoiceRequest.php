<?php

namespace App\Http\Requests\Invoices;

use App\Http\Requests\Request;

class SearchInvoiceRequest extends Request
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
            'with.*' => 'string|in:job,job.simpro_site,job.simpro_customer',
        ];
    }
}