<?php

namespace App\Http\Requests\SiteContacts;

use App\Http\Requests\Request;
use App\Models\SiteContact;
use App\Services\SimproSiteService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateSiteContactRequest extends Request
{
    public function rules()
    {
        return [
            'title' => 'string|max:255|nullable',
            'given_name' => 'string|max:255',
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

        $siteContact = app(SiteContact::class)->find($this->route('id'));

        if (!$siteContact) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'SiteContact']));
        }

        $service = app(SimproSiteService::class);

        if ($this->isUser()) {
            $simproSite = $service->checkGroupPermissions($siteContact['simpro_site_id'], $this->getUserId());
        } else {
            $simproSite = $service->find($siteContact['simpro_site_id']);
        }

        if (!$simproSite) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'SimproSite']));
        }
    }
}
