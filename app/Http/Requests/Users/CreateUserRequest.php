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
        $invoices = implode(',', config('defaults.invoice_permissions'));
        $quotes = implode(',', config('defaults.quote_permissions'));

        return [
            'name' => 'string|required',
            'email' => 'required|email|unique:users,email',
            'group_ids' => 'array',
            'group_ids.*' => 'integer|exists:groups,id',
            'invoices' => "in:$invoices",
            'quotes' => "in:$quotes",
            'is_quote_requests' => 'boolean',
            'is_job_requests' => 'boolean',
            'is_send_email' => 'boolean'
        ];
    }
}