<?php

namespace App\Http\Requests\SimproSites;

use App\Http\Requests\Request;
use App\Services\SimproSiteService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetSimproSiteRequest extends Request
{
    public function rules()
    {
        return [
            'with' => 'array',
            'with.*' => 'string|in:group_simpro_sites,simpro_customer,site_custom_fields,site_contacts,primary_site_contact,reference_site_custom_field,customer_ref_site_custom_field',
            'with_count' => 'array',
            'with_count.*' => 'string|in:open_jobs'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(SimproSiteService::class);

        if ($this->isUser()) {
            $simproSite = $service->checkGroupPermissions($this->route('id'), $this->getUserId());
        } else {
            $simproSite = $service->find($this->route('id'));
        }

        if (!$simproSite) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'SimproSite']));
        }
    }
}
