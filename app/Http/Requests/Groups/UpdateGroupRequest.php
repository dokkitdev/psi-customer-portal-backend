<?php

namespace App\Http\Requests\Groups;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\GroupService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UpdateGroupRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [
            'title' => 'string|max:255',
            'simpro_customer_id' => 'integer|exists:simpro_customers,id'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(GroupService::class);

        $group = $service->find($this->route('id'));

        if (!$group) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Group']));
        }

        $simproCustomerId = $this->get('simpro_customer_id', $group['simpro_customer_id']);

        if ($service->exists(['title' => $this->get('title'), 'simpro_customer_id' => $simproCustomerId])) {
            throw new UnprocessableEntityHttpException(__('validation.exceptions.not_unique', ['entity' => 'Group']));
        }
    }
}
