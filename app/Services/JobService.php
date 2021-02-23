<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\JobRepository;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\EntityService;

/**
 * @property JobRepository $repository
 * @mixin JobRepository
 */
class JobService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected $companyId;
    protected SimproSiteService $simproSiteService;

    public function __construct()
    {
        $this->setRepository(JobRepository::class);

        $this->simproClient = app(SimproApiClient::class);

        $this->companyId = config('services.simpro.company_id');

        $this->simproSiteService = app(SimproSiteService::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterByQuery(['name'])
            ->with()
            ->getSearchResults();
    }

    public function createOrUpdateBySimpro($webhook)
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $jobIdFromSimpro = $webhook['data']['reference']['jobID'];

        $jobFromSimpro = $this->simproClient->getJob($companyId, $jobIdFromSimpro);

        $site = $this->simproSiteService->getOrCreateBySimpro($companyId, $jobFromSimpro['Site']['ID']);

        $job = $this->repository->findBy('job_id', $jobIdFromSimpro);

        if (!$job) {
            $job = $this->createFromSimpro($job, $site['id']);
        } else {
            $job = $this->updateFromSimpro($job['id'], $job);
        }
    }

    public function createFromSimpro($job, $siteId)
    {
        return $this->repository->create([
            'job_id' => $job['ID'],
            'simpro_customer_id',
            'simpro_site_id' => $siteId,
            'description' => Arr::get($job, 'Description'),
            'priority',
            'cost_center_name',
            'business_group',
            'date_created' => Arr::get($job, 'DateIssued'),
            'stage',
            'job_status' => $job['Status']['Name'],
            'requested'
        ]);
    }

    public function updateFromSimpro($id, $job)
    {
        return $this->repository->update($id, [
            'work_order' => Arr::get($job, 'OrderNo'),
            'job_status_id' => $job['Status']['ID'],
            'job_status_name' => $job['Status']['Name'],
            'priority',
            'description' => Arr::get($job, 'Description'),
            'date_open' => Arr::get($job, 'DateIssued'),
            'completion_date' => Arr::get($job, 'CompletedDate')
        ]);
    }

    public function deleteBySimpro($webhook)
    {

    }
}
