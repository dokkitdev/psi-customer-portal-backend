<?php

namespace App\Http\Requests\Setting;

use App\Http\Requests\Request;
use App\Models\Role;

class UpdateDefaultsSettingRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [
            'default_tag' => 'array',
            'default_tag.ID' => 'required|integer',
            'default_tag.Name' => 'required|string',
            'admin_email' => 'array',
            'admin_email.email' => 'required|email',
            'quote_date_created' => 'array',
            'quote_date_created.ID' => 'required|integer',
            'quote_date_created.Name' => 'required|string'
        ];
    }
}
