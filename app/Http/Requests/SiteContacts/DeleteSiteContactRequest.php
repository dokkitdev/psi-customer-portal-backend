<?php

namespace App\Http\Requests\SiteContacts;

use App\Http\Requests\Request;
use App\Models\SimproSite;
use App\Models\SiteContact;
use App\Services\SimproSiteService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteSiteContactRequest extends Request
{
    public function rules()
    {
        return [];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $siteContact = app(SiteContact::class)->find($this->route('id'));

        if (!$siteContact) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'SiteContact']));
        }

        $this->validateExistsByPermissions($siteContact['simpro_site_id'], SimproSite::class, SimproSiteService::class);
    }
}
