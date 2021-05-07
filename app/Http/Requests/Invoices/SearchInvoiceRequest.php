<?php

namespace App\Http\Requests\Invoices;

use App\Http\Requests\Request;
use App\Models\User;

class SearchInvoiceRequest extends Request
{
    public function authorize()
    {
        return $this->user()->invoice_permission_level === User::INVOICE_PERMISSION_LEVEL_VIEW;
    }

    public function rules()
    {
        return [
            'is_paid' => 'boolean',
            'invoice_id' => 'integer',
            'job_id' => 'integer',
            'simpro_job_id' => 'integer',
            'simpro_site_id' => 'integer',
            'simpro_customer_id' => 'integer',
            'statuses' => 'array',
            'statuses.*' => 'string',
            'total' => 'numeric',
            'total_from' => 'numeric',
            'total_to' => 'numeric',
            'date_issued' => 'date',
            'date_issued_from' => 'date',
            'date_issued_to' => 'date',
            'date_paid' => 'date',
            'date_paid_from' => 'date',
            'date_paid_to' => 'date',
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => 'string|in:job,job.simpro_site,job.simpro_customer',
        ];
    }
}