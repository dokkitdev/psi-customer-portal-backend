<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Job;
use App\Models\Role;
use App\Repositories\JobRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @property JobRepository $repository
 * @mixin JobRepository
 */
class JobService extends BaseService
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
        parent::__construct();

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
        $authUser = $this->getAuthUser();

        if ($authUser['role_id'] === Role::USER) {
            $filters['site_has_user'] = $authUser['id'];
        }

        return $this->repository
            ->searchQuery($filters)
            ->filterBy('job_id')
            ->filterBy('simpro_customer_id')
            ->filterBy('simpro_site_id')
            ->filterBy('simpro_customer.name', 'customer_name')
            ->filterBy('simpro_site.name', 'site_name')
            ->filterBy('simpro_site.postal_code')
            ->filterByList('priority', 'priority')
            ->filterBy('cost_center_name')
            ->filterByList('business_group', 'business_group')
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
            ->filterByUserGroups()
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
            'priority' => $this->makePriorityValue(Arr::get($jobFromSimpro, 'ResponseTime')),
            'cost_center_name' => Arr::get($jobFromSimpro, 'Sections.0.CostCenters.0.CostCenter.Name'),
            'business_group' => $this->matchBusinessGroup(Arr::get($jobFromSimpro, 'Sections.0.CostCenters.0.CostCenter.Name')),
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

    protected function matchBusinessGroup($costCenterName)
    {
        foreach (Job::BUSINESS_GROUPS as $businessGroup) {
            $businessGroupName = ($businessGroup === 'Reactives') ? 'Reactive' : $businessGroup;

            if (Str::contains($costCenterName, $businessGroupName)) {
                return $businessGroup;
            }
        }

        return null;
    }

    protected function makePriorityValue($responseTime)
    {
        if (!$responseTime) {
            return null;
        }

        if ($responseTime['Days'] !== 0) {
            $priority = "{$responseTime['Name']} {$responseTime['Days']} Days";
        } elseif ($responseTime['Hours'] !== 0) {
            $priority = "{$responseTime['Name']} {$responseTime['Hours']} Hours";
        } elseif ($responseTime['Minutes'] !== 0) {
            $priority = "{$responseTime['Name']} {$responseTime['Minutes']} Minutes";
        } else {
            $priority = $responseTime['Name'];
        }

        return $priority;
    }
}
