<?php

namespace App\Http\Requests;

use App\Models\Role;
use RonasIT\Support\BaseRequest;

class Request extends BaseRequest
{
    public function getUserId()
    {
        return $this->user()->id;
    }

    public function isUser()
    {
        return $this->user()->role_id === Role::USER;
    }
}