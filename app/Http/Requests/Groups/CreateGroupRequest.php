<?php

namespace App\Http\Requests\Groups;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\GroupService;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CreateGroupRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'simpro_customer_id' => 'required|integer|exists:simpro_customers,id'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(GroupService::class);

        if ($service->exists(['title' => $this->get('title'), 'simpro_customer_id' => $this->get('simpro_customer_id')])) {
            throw new UnprocessableEntityHttpException(__('validation.exceptions.not_unique', ['entity' => 'Group']));
        }
    }
}
