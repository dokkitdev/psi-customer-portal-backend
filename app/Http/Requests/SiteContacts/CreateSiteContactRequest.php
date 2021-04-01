<?php

namespace App\Http\Requests\SiteContacts;

use App\Http\Requests\Request;
use App\Services\SimproSiteService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateSiteContactRequest extends Request
{
    public function rules()
    {
        return [
            'simpro_site_id' => 'required|integer|exists:simpro_sites,id',
            'title' => 'string|max:255|nullable',
            'given_name' => 'required|string|max:255',
            'family_name' => 'string|max:255|nullable',
            'email' => 'string|max:255|nullable',
            'work_phone' => 'string|max:255|nullable',
            'cell_phone' => 'string|max:255|nullable',
            'position' => 'string|max:255|nullable',
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(SimproSiteService::class);

        if ($this->isUser()) {
            $simproSite = $service->checkGroupPermissions($this->get('simpro_site_id'), $this->getUserId());
        } else {
            $simproSite = $service->find($this->get('simpro_site_id'));
        }

        if (!$simproSite) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'SimproSite']));
        }
    }
}
