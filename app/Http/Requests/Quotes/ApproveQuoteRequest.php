<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Models\Quote;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApproveQuoteRequest extends Request
{
    public function rules()
    {
        return [];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $quote = $this->validateExistsByPermissions($this->route('id'), 'Quote');

        if ($quote['stage'] === Quote::STAGE_APPROVED) {
            throw new BadRequestHttpException(__('validation.exceptions.already_processed', ['entity' => 'Quote']));
        }
    }
}
