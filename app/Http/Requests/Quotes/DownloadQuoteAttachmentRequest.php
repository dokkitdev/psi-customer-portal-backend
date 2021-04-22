<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Models\User;
use App\Services\QuoteService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadQuoteAttachmentRequest extends Request
{
    public function authorize()
    {
        return in_array($this->user()->quote_permission_level, [
            User::QUOTE_PERMISSION_LEVEL_VIEW,
            User::QUOTE_PERMISSION_LEVEL_EDIT
        ]);
    }

    public function rules()
    {
        return [];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $quote = app(QuoteService::class)->first($this->route('id'));

        if (!$quote) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Quote']));
        }

        if (empty($quote['attachment_id'])) {
            throw new BadRequestHttpException(__('validation.exceptions.bad_request', ['entity' => 'Quote', 'attribute' => 'attachment_id']));
        }
    }
}
