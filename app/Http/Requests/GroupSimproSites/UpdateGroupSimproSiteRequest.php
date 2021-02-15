<?php

namespace App\Http\Requests\GroupSimproSites;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\GroupSimproSiteService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateGroupSimproSiteRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        if (!app(GroupSimproSiteService::class)->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'GroupSimproSite']));
        }
    }
}
