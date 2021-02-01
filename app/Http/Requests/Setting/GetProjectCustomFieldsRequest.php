<?php

namespace App\Http\Requests\Setting;

use App\Http\Requests\Request;
use App\Models\Role;

class GetProjectCustomFieldsRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [];
    }
}
