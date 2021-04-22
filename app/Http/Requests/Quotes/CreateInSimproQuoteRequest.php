<?php

namespace App\Http\Requests\Quotes;

use App\Http\Requests\Request;
use App\Models\Quote;

class CreateInSimproQuoteRequest extends Request
{
    public function authorize()
    {
        return $this->user()->is_quote_requests;
    }

    public function rules()
    {
        $mediaTypes = implode(',', config('defaults.permitted_media_types'));

        $quoteTypes = implode(',', Quote::TYPES);

        return [
            'simpro_site_id' => 'required|integer',
            'type' => "required|in:{$quoteTypes}",
            'description' => 'string',
            'files' => 'array',
            'files.*' => "file|required|max:5120|mimes:{$mediaTypes}"
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->validateExistsByPermissions($this->get('simpro_site_id'), 'SimproSite');
    }
}
