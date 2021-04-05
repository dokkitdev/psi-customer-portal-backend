<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Models\Quote;
use App\Services\QuoteService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ApproveQuoteRequest extends Request
{
    public function rules()
    {
        return [];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(QuoteService::class);

        if ($this->isUser()) {
            $quote = $service->checkGroupPermissions($this->route('id'), $this->getUserId());
        } else {
            $quote = $service->find($this->route('id'));
        }

        if (!$quote) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Quote']));
        }

        if ($quote['stage'] === Quote::STAGE_APPROVED) {
            throw new UnprocessableEntityHttpException(__('validation.exceptions.incorrect_attribute', ['attribute' => 'stage']));
        }
    }
}
