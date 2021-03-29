<?php

namespace App\Http\Requests\Jobs;

use App\Http\Requests\Request;
use App\Services\SimproSiteService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateInSimproJobRequest extends Request
{
    public function rules()
    {
        $types = implode(',', config('defaults.permitted_media_types'));

        return [
            'simpro_site_id' => 'required|integer',
            'description' => 'string',
            'files' => 'array',
            'file.*' => "file|required|max:5120|mimes:{$types}"
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
