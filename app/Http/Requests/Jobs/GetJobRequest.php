<?php

namespace App\Http\Requests\Jobs;

use App\Http\Requests\Request;

class GetJobRequest extends Request
{
    public function rules()
    {
        return [
            'with' => 'array',
            'with.*' => 'string|in:simpro_site,simpro_customer,recent_schedule,schedules,job_catalogs,job_attachments,job_work_orders'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->validateExistsByPermissions($this->route('id'), 'Job');

    }
}
