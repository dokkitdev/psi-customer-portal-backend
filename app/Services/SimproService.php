<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;

class SimproService
{
    protected SimproApiClient $simproClient;
    protected $companyId;

    public function __construct()
    {
        $this->companyId = config('services.simpro.company_id');

        $this->simproClient = app(SimproApiClient::class);
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
            250,
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
}