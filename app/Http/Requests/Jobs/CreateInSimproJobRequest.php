<?php

namespace App\Http\Requests\Jobs;

use App\Http\Requests\Request;

class CreateInSimproJobRequest extends Request
{
    public function rules()
    {
        $types = implode(',', config('defaults.permitted_media_types'));

        return [
            'simpro_site_id' => 'required|integer',
            'description' => 'string',
            'files' => 'array',
            'files.*' => "file|required|max:5120|mimes:{$types}"
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->validateExistsByPermissions($this->get('simpro_site_id'), 'SimproSite');
    }
}
