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
    protected JobCatalogService $jobCatalogService;
    protected JobAttachmentService $jobAttachmentService;
    protected JobWorkOrderService $jobWorkOrderService;

    public function __construct()
    {
        $this->setRepository(JobRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->settingService = app(SettingService::class);
        $this->companyId = config('services.simpro.company_id');
        $this->simproSiteService = app(SimproSiteService::class);
        $this->simproCustomerService = app(SimproCustomerService::class);
        $this->jobCatalogService = app(JobCatalogService::class);
        $this->jobAttachmentService = app(JobAttachmentService::class);
        $this->jobWorkOrderService = app(JobWorkOrderService::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterBy('job_id')
            ->filterBy('simpro_customer.name', 'customer_name')
            ->filterBy('simpro_site.name', 'site_name')
            ->filterBy('simpro_site.postal_code')
            ->filterByList('priority', 'priority')
            ->filterBy('cost_center_name')
            ->filterBy('business_group')
            ->filterByList('stage', 'stage')
            ->filterByList('job_status', 'job_status')
            ->filterByRequested()
            ->filterFrom('recent_schedule.date', false, 'appointment_from')
            ->filterTo('recent_schedule.date', false, 'appointment_to')
            ->filterFrom('recent_schedule.start_time', false, 'start_time_from')
            ->filterTo('recent_schedule.start_time', false, 'start_time_to')
            ->filterFrom('recent_schedule.end_time', false, 'end_time_from')
            ->filterTo('recent_schedule.end_time', false, 'end_time_to')
            ->filterByQuery(['simpro_site.name', 'simpro_site.postal_code', 'simpro_customer.name'])
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

        $job = $this->repository->updateOrCreate(['job_id' => $jobFromSimpro['ID']], [
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

        app(ScheduleService::class)->createOrUpdateManyBySimpro($companyId, $jobIdFromSimpro, $job['id']);

        $this->jobCatalogService->syncBySimpro($jobFromSimpro, $job['id']);

        $this->jobAttachmentService->syncBySimpro($companyId, $jobIdFromSimpro, $job['id']);

        $this->jobWorkOrderService->syncBySimpro($companyId, $jobFromSimpro, $job['id']);

        return $job;
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
