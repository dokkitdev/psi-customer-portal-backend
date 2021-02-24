<?php

namespace App\Http\Requests\SimproCustomers;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\SimproCustomerService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetSimproCustomerRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [
            'with' => 'array',
            'with.*' => 'string|in:groups'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(SimproCustomerService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'SimproCustomer']));
        }
    }
}
