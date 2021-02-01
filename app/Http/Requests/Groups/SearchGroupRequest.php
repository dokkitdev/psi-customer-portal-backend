<?php

namespace App\Http\Requests\Groups;

use App\Http\Requests\Request;

class SearchGroupRequest extends Request
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
            'with.*' => 'string|in:simpro_customer'
        ];
    }
}