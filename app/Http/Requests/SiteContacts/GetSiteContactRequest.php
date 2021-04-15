<?php

namespace App\Http\Requests\SiteContacts;

use App\Http\Requests\Request;
use App\Models\SiteContact;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetSiteContactRequest extends Request
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

        $this->validateExistsByPermissions($siteContact['simpro_site_id'], 'SimproSite');
    }
}
