<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Job;
use App\Models\Quote;
use App\Models\Role;
use App\Models\User;
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

    public function getDashboardCounters(User $user)
    {
        $onlyPermittedForUserId = ($user->role_id === Role::USER)
            ? $user->id
            : null;

        $jobsCount = $this->jobService->countGroupedByStage($onlyPermittedForUserId);

        $pendingQuotesCount = $this->quoteService->countByStatusAndStage($onlyPermittedForUserId, Quote::STATUS_PENDING, Quote::STAGE_SENT);

        $notPaidInvoicesCount = $this->invoiceService->countNotPaid($onlyPermittedForUserId);

        return [
            'pending_jobs_total' => Arr::get($jobsCount, Job::PENDING_STAGE, 0),
            'progress_jobs_total' => Arr::get($jobsCount, Job::PROGRESS_STAGE, 0),
            'complete_jobs_total' => Arr::get($jobsCount, Job::COMPLETE_STAGE, 0),
            'archived_jobs_total' => Arr::get($jobsCount, Job::ARCHIVED_STAGE, 0),
            'invoiced_jobs_total' => Arr::get($jobsCount, Job::INVOICED_STAGE, 0),
            'pending_quotes_total' => $pendingQuotesCount,
            'unpaid_invoices_total' => $notPaidInvoicesCount,
        ];
    }
}