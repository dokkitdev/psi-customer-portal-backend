<?php

namespace App\Http\Requests\SimproCustomers;

use App\Http\Requests\Request;
use App\Models\Role;

class SearchSimproCustomerRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

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