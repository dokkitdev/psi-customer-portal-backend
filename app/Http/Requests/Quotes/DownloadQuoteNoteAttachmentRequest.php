<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Services\QuoteService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadQuoteNoteAttachmentRequest extends Request
{
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

        if (empty($quote['note_id'])) {
            throw new BadRequestHttpException(__('validation.exceptions.bad_request', ['entity' => 'Quote', 'attribute' => 'note_id']));
        }

        if (empty($quote['attachment_id'])) {
            throw new BadRequestHttpException(__('validation.exceptions.bad_request', ['entity' => 'Quote', 'attribute' => 'attachment_id']));
        }
    }
}
