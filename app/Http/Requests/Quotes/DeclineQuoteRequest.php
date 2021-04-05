<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Models\Quote;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DeclineQuoteRequest extends Request
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

        $quote = $this->validateExistsByPermissions($this->route('id'), 'Quote');

        if (!in_array($quote['status'], [Quote::STATUS_NEW, Quote::STATUS_PENDING])) {
            throw new BadRequestHttpException(__('validation.exceptions.already_processed', ['entity' => 'Quote']));
        }
    }
}
