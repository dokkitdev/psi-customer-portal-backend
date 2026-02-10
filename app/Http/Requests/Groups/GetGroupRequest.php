<?php

namespace App\Http\Requests\Groups;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\GroupService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetGroupRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [
            'with' => 'array',
            'with.*' => 'string|in:simpro_customer'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(GroupService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Group']));
        }
    }
}
