<?php

namespace App\Http\Requests\Groups;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\GroupService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChangeSitesVisibilityRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [
            'is_enabled' => 'required|boolean'
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
    }
}
