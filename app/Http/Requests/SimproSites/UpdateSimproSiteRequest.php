<?php

namespace App\Http\Requests\SimproSites;

use App\Http\Requests\Request;
use App\Models\SimproSite;
use App\Services\SimproSiteService;
use App\Services\SiteContactService;
use App\Services\SiteCustomFieldService;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UpdateSimproSiteRequest extends Request
{
    public function rules()
    {
        return [
            'name' => 'string',
            'address' => 'string|nullable',
            'postal_code' => 'string|nullable',
            'city' => 'string|nullable',
            'country' => 'string|nullable',
            'county' => 'string|nullable',
            'primary_site_contact_id' => 'integer',
            'site_custom_fields' => 'array',
            'site_custom_fields.*.id' => 'required|integer',
            'site_custom_fields.*.value' => 'string|nullable'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->validateExistsByPermissions($this->route('id'), SimproSite::class, SimproSiteService::class);

        if ($this->has('primary_site_contact_id')) {
            $this->checkPrimarySiteContact();
        }

        if ($this->has('site_custom_fields')) {
            $this->checkCustomFields();
        }
    }

    protected function checkCustomFields()
    {
        $service = app(SiteCustomFieldService::class);

        foreach ($this->get('site_custom_fields') as $customField) {
            $exists = $service->exists([
                'id' => $customField['id'],
                'simpro_site_id' => $this->route('id'),
            ]);

            if (!$exists) {
                throw new UnprocessableEntityHttpException(__('validation.exceptions.not_found', ['entity' => 'SiteCustomField']));
            }
        }
    }

    protected function checkPrimarySiteContact()
    {
        $service = app(SiteContactService::class);

        $exists = $service->exists([
            'id' => $this->get('primary_site_contact_id'),
            'simpro_site_id' => $this->route('id'),
        ]);

        if (!$exists) {
            throw new UnprocessableEntityHttpException(__('validation.exceptions.not_found', ['entity' => 'SiteContact']));
        }
    }
}
