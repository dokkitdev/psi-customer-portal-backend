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
    protected SettingService $settingService;
    protected $companyId;
    protected SimproSiteService $simproSiteService;
    protected SimproCustomerService $simproCustomerService;

    public function __construct()
    {
        $this->setRepository(JobRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->settingService = app(SettingService::class);
        $this->companyId = config('services.simpro.company_id');
        $this->simproSiteService = app(SimproSiteService::class);
        $this->simproCustomerService = app(SimproCustomerService::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->with()
            ->getSearchResults();
    }

    public function createOrUpdateBySimpro($webhook)
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $jobIdFromSimpro = $webhook['data']['reference']['jobID'];

        $jobFromSimpro = $this->simproClient->getJob($companyId, $jobIdFromSimpro);

        $simproSite = $this->simproSiteService->getOrCreateBySimpro($companyId, $jobFromSimpro['Site']['ID']);

        $simproCustomer = $this->simproCustomerService->getOrCreateBySimpro($companyId, $jobFromSimpro['Customer']);

        $customField = $this->findCustomFieldById(Arr::get($jobFromSimpro, 'CustomFields', []));

        return $this->repository->updateOrCreate(['job_id' => $jobFromSimpro['ID']], [
            'simpro_customer_id' => $simproCustomer['id'],
            'simpro_site_id' => $simproSite['id'],
            'description' => Arr::get($jobFromSimpro, 'Description'),
            'priority' => Arr::get($jobFromSimpro, 'ResponseTime.Name'),
            'cost_center_name' => Arr::get($jobFromSimpro, 'Sections.0.CostCenters.0.CostCenter.Name'),
            'business_group' => Arr::get($jobFromSimpro, 'Sections.0.CostCenters.0.CostCenter.Name'),
            'date_created' => Arr::get($jobFromSimpro, 'DateIssued'),
            'stage' => Arr::get($jobFromSimpro, 'Stage'),
            'job_status' => Arr::get($jobFromSimpro, 'Status.Name'),
            'requested' => Arr::get($customField, 'Value')
        ]);
    }

    public function deleteBySimpro($webhook)
    {
        $jobIdFromSimpro = $webhook['data']['reference']['jobID'];

        return $this->repository->delete(['job_id' => $jobIdFromSimpro]);
    }

    protected function findCustomFieldById($customFields)
    {
        $defaultTagId = Arr::get($this->settingService->get('default_tag'), 'ID');

        return collect($customFields)->first(function ($value) use ($defaultTagId) {
            return Arr::get($value, 'CustomField.ID') === $defaultTagId;
        }, []);
    }
}
