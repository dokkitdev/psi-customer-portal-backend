<?php

namespace App\Http\Requests\Assets;

use App\Http\Requests\Request;

class SearchAssetRequest extends Request
{
    public function rules()
    {
        return [
            'asset_id' => 'integer',
            'simpro_customer_id' => 'integer',
            'simpro_site_id' => 'integer',
            'parent_id' => 'integer',
            'type' => 'string',
            'archived' => 'boolean',
            'last_test_date' => 'date',
            'last_test_date_from' => 'date',
            'last_test_date_to' => 'date',
            'next_service_date' => 'date',
            'next_service_date_from' => 'date',
            'next_service_date_to' => 'date',
            'last_test_result_query' => 'string',
            'service_level_name_query' => 'string',
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => 'string|in:simpro_site,simpro_customer,asset_custom_fields,asset_attachments,asset_test_records,asset_test_records.job,asset_test_records.asset_test_record_readings',
        ];
    }
}
