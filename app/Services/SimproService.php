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
        return $this->simproClient->getProjectTags($this->companyId);
    }

    public function getProjectCustomFields()
    {
        return $this->simproClient->getProjectCustomFields($this->companyId);
    }
}