<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Models\Quote;
use App\Models\User;
use App\Services\QuoteService;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReRequestQuoteRequest extends Request
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

        if (($quote['status'] !== Quote::STATUS_DECLINED) && Carbon::createFromFormat('Y-m-d', $quote['date_expiry'])->greaterThan(now())) {
            throw new BadRequestHttpException(__('validation.exceptions.already_processed', ['entity' => 'Quote']));
        }

        $wrongStatuses = config('defaults.wrong_quotes_re_request_statuses');

        if (in_array($quote['status_id'], $wrongStatuses)) {
            throw new BadRequestHttpException(__('validation.exceptions.wrong_status', ['entity' => 'Quote']));
        }
    }
}
