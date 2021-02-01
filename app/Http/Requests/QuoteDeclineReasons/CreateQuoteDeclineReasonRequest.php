<?php

namespace App\Http\Requests\QuoteDeclineReasons;

use App\Http\Requests\Request;
use App\Models\Role;

class CreateQuoteDeclineReasonRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
        ];
    }
}
