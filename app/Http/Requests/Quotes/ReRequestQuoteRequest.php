<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Models\Quote;
use App\Services\QuoteService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReRequestQuoteRequest extends Request
{
    public function rules()
    {
        return [
            'reason' => 'string|max:500|nullable'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $quote = $this->validateExistsByPermissions($this->route('id'), Quote::class, QuoteService::class);

        if ($quote['status'] !== Quote::STATUS_DECLINED) {
            throw new BadRequestHttpException(__('validation.exceptions.already_processed', ['entity' => 'Quote']));
        }
    }
}
