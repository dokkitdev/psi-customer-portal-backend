<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Models\Quote;
use App\Services\SimproSiteService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateInSimproQuoteRequest extends Request
{
    public function rules()
    {
        $mediaTypes = implode(',', config('defaults.permitted_media_types'));

        $quoteTypes = implode(',', Quote::TYPES);

        return [
            'simpro_site_id' => 'required|integer',
            'type' => "required|in:{$quoteTypes}",
            'description' => 'string',
            'files' => 'array',
            'files.*' => "file|required|max:5120|mimes:{$mediaTypes}"
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
