<?php

namespace App\Http\Requests\QuoteStatusCodes;

use App\Http\Requests\Request;
use App\Services\QuoteStatusCodeService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetQuoteStatusCodeRequest extends Request
{
    public function rules()
    {
        return [];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(QuoteStatusCodeService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'QuoteStatusCode']));
        }
    }
}
