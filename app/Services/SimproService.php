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
}