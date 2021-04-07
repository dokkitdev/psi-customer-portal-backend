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
    protected InvoiceService $invoiceService;

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
        $this->invoiceService = app(InvoiceService::class);
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
            ->filterByPostalCode()
            ->filterByPriority()
            ->filterByList('cost_center_name', 'cost_center_name')
            ->filterByList('business_group', 'business_group')
            ->filterByList('stage', 'stage')
            ->filterByList('job_status', 'job_status')
            ->filterByRequested()
            ->filterByRecentSchedule()
            ->filterByQuery(['simpro_site.name', 'simpro_site.postal_code', 'simpro_customer.name'])
            ->filterByUserGroups()
            ->with()
            ->getSearchResults();
    }

    public function createInSimpro($data)
    {
        $simproSite = $this->simproSiteService->withRelations(['simpro_customer'])->find($data['simpro_site_id']);

        $defaultTag = $this->settingService->get('default_tag');

        $jobData = [
            'Type' => 'Project',
            'Customer' => Arr::get($simproSite, 'simpro_customer.customer_id'),
            'Site' => $simproSite['site_id'],
            'Tags' => [$defaultTag['ID']]
        ];

        if (Arr::has($data, 'description')) {
            $jobData['Description'] = $data['description'];
        }

        $job = $this->simproClient->postJob($this->companyId, $jobData);

        if (Arr::has($data, 'files')) {
            foreach ($data['files'] as $file) {
                $this->simproClient->postJobAttachment($this->companyId, $job['ID'], [
                    'Filename' => $file['filename'],
                    'Base64Data' => base64_encode($file['content']),
                    'Public' => true
                ]);
            }
        }

        return $job;
    }

    public function createOrUpdateBySimpro($webhook)
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $jobIdFromSimpro = $webhook['data']['reference']['jobID'];

        $jobFromSimpro = $this->simproClient->getJob($companyId, $jobIdFromSimpro);

        $simproCustomer = $this->simproCustomerService->getOrCreateBySimpro($companyId, $jobFromSimpro['Customer']);

        $simproSite = $this->simproSiteService->getOrCreateBySimpro($companyId, $jobFromSimpro['Site']['ID'], $simproCustomer['id']);

        $job = $this->createOrUpdate($jobFromSimpro, $simproCustomer['id'], $simproSite['id']);

        app(ScheduleService::class)->createOrUpdateManyBySimpro($companyId, $jobIdFromSimpro, $job['id']);

        $this->jobCatalogService->syncBySimpro($jobFromSimpro, $job['id']);

        $this->jobAttachmentService->syncBySimpro($companyId, $jobIdFromSimpro, $job['id']);

        $this->jobWorkOrderService->syncBySimpro($companyId, $jobFromSimpro, $job['id']);

        $this->invoiceService->syncBySimpro($companyId, $jobIdFromSimpro, $job['id']);

        return $job;
    }

    public function getOrCreateBySimpro($companyId, $jobIdFromSimpro)
    {
        $job = $this->repository->findBy('job_id', $jobIdFromSimpro);

        if ($job) {
            return $job;
        }

        $jobFromSimpro = $this->simproClient->getJob($companyId, $jobIdFromSimpro);

        $simproCustomer = $this->simproCustomerService->getOrCreateBySimpro($companyId, $jobFromSimpro['Customer']);

        $simproSite = $this->simproSiteService->getOrCreateBySimpro($companyId, $jobFromSimpro['Site']['ID'], $simproCustomer['id']);

        return $this->createOrUpdate($jobFromSimpro, $simproCustomer['id'], $simproSite['id']);
    }

    protected function createOrUpdate($jobFromSimpro, $simproCustomerId, $simproSiteId)
    {
        $customField = $this->findCustomFieldById(Arr::get($jobFromSimpro, 'CustomFields', []));

        return $this->repository->updateOrCreate(['job_id' => $jobFromSimpro['ID']], [
            'simpro_customer_id' => $simproCustomerId,
            'simpro_site_id' => $simproSiteId,
            'description' => Arr::get($jobFromSimpro, 'Description'),
            'priority' => $this->makePriorityValue(Arr::get($jobFromSimpro, 'ResponseTime')),
            'cost_center_name' => Arr::get($jobFromSimpro, 'Sections.0.CostCenters.0.CostCenter.Name'),
            'business_group' => $this->matchBusinessGroup(Arr::get($jobFromSimpro, 'Sections.0.CostCenters.0.CostCenter.Name')),
            'date_created' => Arr::get($jobFromSimpro, 'DateIssued'),
            'stage' => Arr::get($jobFromSimpro, 'Stage'),
            'job_status' => Arr::get($jobFromSimpro, 'Status.Name'),
            'requested' => Arr::get($customField, 'Value'),
            'name' => Arr::get($jobFromSimpro, 'Name'),
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
