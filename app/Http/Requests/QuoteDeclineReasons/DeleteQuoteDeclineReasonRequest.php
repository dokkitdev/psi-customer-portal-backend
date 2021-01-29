<?php

namespace App\Http\Requests\QuoteDeclineReasons;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\QuoteDeclineReasonService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteQuoteDeclineReasonRequest extends Request
{
    public function authorize()
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules()
    {
        return [];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(QuoteDeclineReasonService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'QuoteDeclineReason']));
        }
    }
}
