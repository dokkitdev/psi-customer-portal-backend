<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Models\Quote;
use App\Models\User;
use App\Services\QuoteService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeclineQuoteRequest extends Request
{
    public function authorize()
    {
        return $this->user()->quote_permission_level === User::QUOTE_PERMISSION_LEVEL_EDIT;
    }

    public function rules()
    {
        return [
            'reason' => 'string|max:500|nullable'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $quote = app(QuoteService::class)->find($this->route('id'));

        if (!$quote) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Quote']));
        }

        if (($quote['stage'] !== Quote::STAGE_SENT) || !in_array($quote['status'], [Quote::STATUS_NEW, Quote::STATUS_PENDING])) {
            throw new BadRequestHttpException(__('validation.exceptions.already_processed', ['entity' => 'Quote']));
        }
    }
}
