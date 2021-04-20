<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Models\Quote;
use App\Models\User;
use App\Services\QuoteService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApproveQuoteRequest extends Request
{
    public function authorize()
    {
        return $this->user()->quote_permission_level === User::QUOTE_PERMISSION_LEVEL_EDIT;
    }

    public function rules()
    {
        return [];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $quote = app(QuoteService::class)->find($this->route('id'));

        if (!$quote) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Quote']));
        }

        if ($quote['stage'] === Quote::STAGE_APPROVED) {
            throw new BadRequestHttpException(__('validation.exceptions.already_processed', ['entity' => 'Quote']));
        }
    }
}
