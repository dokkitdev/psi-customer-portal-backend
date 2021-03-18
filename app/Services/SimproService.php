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
        $projectTagPages = $this->simproClient->getProjectTagsAsGenerator($this->companyId);

        $projectTags = [];
        foreach ($projectTagPages as $projectTagPage) {
            $projectTags = array_merge($projectTags, $projectTagPage);
        }

        return $projectTags;
    }

    public function getProjectCustomFields()
    {
        $projectCustomFieldPages = $this->simproClient->getProjectCustomFieldsAsGenerator($this->companyId);

        $projectCustomFields = [];
        foreach ($projectCustomFieldPages as $projectCustomFieldPage) {
            $projectCustomFields = array_merge($projectCustomFields, $projectCustomFieldPage);
        }

        return $projectCustomFields;
    }

    public function getResponseTimes()
    {
        $responseTimePages = $this->simproClient->getResponseTimesAsGenerator($this->companyId);

        $responseTimes = [];
        foreach ($responseTimePages as $responseTimePage) {
            $responseTimes = array_merge($responseTimes, $responseTimePage);
        }

        return $responseTimes;
    }

    public function getCostCenters()
    {
        $costCenterPages = $this->simproClient->getCostCentersAsGenerator($this->companyId);

        $costCenters = [];
        foreach ($costCenterPages as $costCenterPage) {
            $costCenters = array_merge($costCenters, $costCenterPage);
        }

        return $costCenters;
    }

    public function getBusinessGroups()
    {
        $businessGroupPages = $this->simproClient->getBusinessGroupsAsGenerator($this->companyId);

        $businessGroups = [];
        foreach ($businessGroupPages as $businessGroupPage) {
            $businessGroups = array_merge($businessGroups, $businessGroupPage);
        }

        return $businessGroups;
    }
}