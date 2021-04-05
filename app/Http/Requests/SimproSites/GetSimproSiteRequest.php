<?php

namespace App\Http\Requests\SimproSites;

use App\Http\Requests\Request;

class GetSimproSiteRequest extends Request
{
    public function rules()
    {
        $with = implode(',', [
            'group_simpro_sites',
            'simpro_customer',
            'site_custom_fields',
            'site_contacts',
            'primary_site_contact',
            'reference_site_custom_field',
            'customer_ref_site_custom_field'
        ]);

        return [
            'with' => 'array',
            'with.*' => "string|in:{$with}",
            'with_count' => 'array',
            'with_count.*' => 'string|in:open_jobs'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->validateExistsByPermissions($this->route('id'), 'SimproSite');
    }
}
