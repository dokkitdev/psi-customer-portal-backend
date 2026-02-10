<?php

namespace App\Http\Requests\QuoteStatusCodes;

use App\Http\Requests\Request;
use App\Models\Quote;
use App\Models\Role;
use App\Services\QuoteStatusCodeService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateQuoteStatusCodeRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        $statuses = implode(',', Quote::STATUSES);

        return [
            'status' => "in:{$statuses}|nullable",
        ];
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
