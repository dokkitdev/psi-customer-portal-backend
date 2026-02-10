<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;

class GetUserProfileRequest extends Request
{
    public function rules()
    {
        return [
            'with' => 'array',
            'with.*' => 'string|in:groups,groups.simpro_customer'
        ];
    }
}
