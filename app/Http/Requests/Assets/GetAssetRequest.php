<?php

namespace App\Http\Requests\Assets;

use App\Http\Requests\Request;

class GetAssetRequest extends Request
{
    public function rules()
    {
        return [
            'with' => 'array',
            'with.*' => 'string|in:simpro_site,simpro_customer,asset_custom_fields,asset_attachments,asset_test_records,asset_test_records.asset_test_record_readings',
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->validateExistsByPermissions($this->route('id'), 'Asset');
    }
}
