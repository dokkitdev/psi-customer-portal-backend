<?php

namespace App\Http\Requests\Documents;

use App\Http\Requests\Request;
use App\Models\Role;

class CreateDocumentRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [
            'media_id' => 'required|exists:media,id',
            'title' => 'string|nullable',
            'description' => 'string|nullable'
        ];
    }
}
