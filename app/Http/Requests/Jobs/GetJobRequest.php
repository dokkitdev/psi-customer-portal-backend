<?php

namespace App\Http\Requests\Jobs;

use App\Http\Requests\Request;
use App\Services\JobService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $service = app(JobService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Job']));
        }
    }
}
