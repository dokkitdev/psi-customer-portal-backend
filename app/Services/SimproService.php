<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Job;
use App\Models\Quote;
use Illuminate\Support\Arr;

class SimproService
{
    protected SimproApiClient $simproClient;
    protected $companyId;
    protected JobService $jobService;
    protected QuoteService $quoteService;
    protected InvoiceService $invoiceService;

    public function __construct()
    {
        $this->companyId = config('services.simpro.company_id');

        $this->simproClient = app(SimproApiClient::class);
        $this->jobService = app(JobService::class);
        $this->quoteService = app(QuoteService::class);
        $this->invoiceService = app(InvoiceService::class);
    }

    public function getProjectTags()
    {
        $projectTagPages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/setup/tags/projects/");

        $projectTags = [];
        foreach ($projectTagPages as $projectTagPage) {
            $projectTags = array_merge($projectTags, $projectTagPage);
        }

        return $projectTags;
    }

    public function getProjectCustomFields()
    {
        $projectCustomFieldPages = $this->simproClient->getAsGenerator(
            "companies/{$this->companyId}/setup/customFields/projects/",
            ['ShowFor.Quotes' => 'true']
        );

        $projectCustomFields = [];
        foreach ($projectCustomFieldPages as $projectCustomFieldPage) {
            $projectCustomFields = array_merge($projectCustomFields, $projectCustomFieldPage);
        }

        return $projectCustomFields;
    }

    public function getResponseTimes()
    {
        $responseTimePages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/setup/responseTimes/");

        $responseTimes = [];
        foreach ($responseTimePages as $responseTimePage) {
            $responseTimes = array_merge($responseTimes, $responseTimePage);
        }

        return $responseTimes;
    }

    public function getCostCenters()
    {
        $costCenterPages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/setup/accounts/costCenters/");

        $costCenters = [];
        foreach ($costCenterPages as $costCenterPage) {
            $costCenters = array_merge($costCenters, $costCenterPage);
        }

        return $costCenters;
    }

    public function getBusinessGroups()
    {
        $businessGroupPages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/setup/accounts/businessGroups/");

        $businessGroups = [];
        foreach ($businessGroupPages as $businessGroupPage) {
            $businessGroups = array_merge($businessGroups, $businessGroupPage);
        }

        return $businessGroups;
    }

    public function getAssetServiceLevels()
    {
        $assetServiceLevelPages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/setup/assets/serviceLevels/");

        $serviceLevels = [];
        foreach ($assetServiceLevelPages as $assetServiceLevelPage) {
            $serviceLevels = array_merge($serviceLevels, $assetServiceLevelPage);
        }

        return $serviceLevels;
    }

    public function getDashboardCounters()
    {
        $data = [
            'per_page' => 0,
            'all' => 1,
        ];

        $data['stage'] = [Job::PENDING_STAGE];
        $pendingJobs = $this->jobService->search($data);

        $data['stage'] = [Job::PROGRESS_STAGE];
        $progressJobs = $this->jobService->search($data);

        $data['stage'] = [Job::COMPLETE_STAGE];
        $completeJobs = $this->jobService->search($data);

        $data['stage'] = [Job::ARCHIVED_STAGE];
        $archivedJobs = $this->jobService->search($data);

        $data = Arr::except($data, 'stage');
        $data['statuses'] = [Quote::STATUS_PENDING];
        $pendingQuotes = $this->quoteService->search($data);

        $data = Arr::except($data, 'statuses');
        $data['is_paid'] = false;
        $unpaidInvoices = $this->invoiceService->search($data);

        return [
            'pending_jobs_total' => $pendingJobs['total'],
            'progress_jobs_total' => $progressJobs['total'],
            'complete_jobs_total' => $completeJobs['total'],
            'archived_jobs_total' => $archivedJobs['total'],
            'pending_quotes_total' => $pendingQuotes['total'],
            'unpaid_invoices_total' => $unpaidInvoices['total'],
        ];
    }
}