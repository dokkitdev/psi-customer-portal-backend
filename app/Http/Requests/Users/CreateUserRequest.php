<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use App\Models\Role;

class CreateUserRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [
            'name' => 'string|required',
            'email' => 'required|email|unique:users,email',
        ];
    }
}