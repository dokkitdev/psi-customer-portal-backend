<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\JobLogRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property JobLogRepository $repository
 * @mixin JobLogRepository
 */
class JobLogService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected $companyId;

    public function __construct()
    {
        $this->setRepository(JobLogRepository::class);

        $this->simproClient = app(SimproApiClient::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function saveAllJobs()
    {
        $jobPages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/jobs/");

        foreach ($jobPages as $jobPage) {
            foreach ($jobPage as $jobFromSimpro) {
                $this->createOrUpdate($jobFromSimpro);
            }
        }
    }

    protected function createOrUpdate($job)
    {
        return $this->repository->updateOrCreate(['job_id' => $job['ID']], []);
    }
}
