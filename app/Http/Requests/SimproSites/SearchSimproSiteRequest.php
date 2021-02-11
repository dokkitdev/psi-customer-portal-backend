<?php

namespace App\Http\Requests\SimproSites;

use App\Http\Requests\Request;
use App\Models\Role;

class SearchSimproSiteRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [
            'group_id' => 'integer',
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => 'string|in:group_simpro_sites'
        ];
    }
}