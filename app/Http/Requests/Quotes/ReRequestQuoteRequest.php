<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Models\Quote;
use App\Models\User;
use App\Services\QuoteService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
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

        $quote = app(QuoteService::class)->withRelations(['quote_status_code'])->find($this->route('id'));

        if (!$quote) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Quote']));
        }

        $quoteStatus = Arr::get($quote, 'quote_status_code.status');

        if (!(($quoteStatus === Quote::STATUS_DECLINED) || (($quoteStatus === Quote::STATUS_PENDING) && Carbon::createFromFormat('Y-m-d', $quote['date_expiry'])->lessThan(now())))) {
            throw new BadRequestHttpException(__('validation.exceptions.already_processed', ['entity' => 'Quote']));
        }
    }
}
