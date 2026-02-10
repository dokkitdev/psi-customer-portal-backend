<?php

namespace App\Http\Requests\Jobs;

use App\Http\Requests\Request;

class SearchJobRequest extends Request
{
    public function rules()
    {
        return [
            'job_id' => 'integer',
            'simpro_customer_id' => 'integer',
            'simpro_site_id' => 'integer',
            'customer_name' => 'string',
            'site_name' => 'string',
            'postal_code' => 'string',
            'priority' => 'array',
            'priority.*' => 'string',
            'cost_center_name' => 'array',
            'cost_center_name.*' => 'string',
            'business_group' => 'array',
            'business_group.*' => 'string',
            'stage' => 'array',
            'stage.*' => 'string',
            'job_status' => 'array',
            'job_status.*' => 'string',
            'requested' => 'boolean',
            'appointment_from' => 'date',
            'appointment_to' => 'date',
            'start_time_from' => 'date',
            'start_time_to' => 'date',
            'end_time_from' => 'date',
            'end_time_to' => 'date',
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => 'string|in:simpro_site,simpro_customer,recent_schedule,schedules,job_catalogs,job_attachments,job_work_orders,invoices,quotes,converted_from_quote'
        ];
    }
}