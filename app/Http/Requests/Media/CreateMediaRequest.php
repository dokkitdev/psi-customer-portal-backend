<?php

namespace App\Http\Requests\Media;

use App\Http\Requests\Request;

class CreateMediaRequest extends Request
{
    public function rules()
    {
        $types = implode(',', config('defaults.permitted_media_types'));
        $maxSize = config('defaults.max_media_size');

        return [
            'file' => "file|required|max:{$maxSize}|mimes:{$types}",
            'is_public' => 'boolean',
        ];
    }
}
