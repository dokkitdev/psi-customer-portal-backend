<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\UserService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateUserRequest extends Request
{
    public function authorize()
    {
        return ($this->user()->role_id === Role::ADMIN) || ($this->user()->id == $this->route('id'));
    }

    public function rules()
    {
        $invoices = implode(',', config('defaults.invoice_permissions'));
        $quotes = implode(',', config('defaults.quote_permissions'));

        return [
            'email' => "string|email|unique:users,email,{$this->route('id')}",
            'name' => 'string',
            'group_ids' => 'array|nullable',
            'group_ids.*' => 'integer|exists:groups,id',
            'invoice_permission_level' => "in:{$invoices}",
            'quote_permission_level' => "in:{$quotes}",
            'is_quote_requests' => 'boolean',
            'is_job_requests' => 'boolean',
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(UserService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'User']));
        }
    }
}
