<?php

namespace App\ApiClients;

use RonasIT\Support\Services\HttpRequestService;

class SimproApiClient
{
    protected HttpRequestService $httpRequestService;

    public function __construct()
    {
        $this->httpRequestService = app(HttpRequestService::class);
    }

    public function getCustomers($companyId, $type, $data = [])
    {
        $url = $this->getUrl("companies/{$companyId}/customers/{$type}/");

        return $this->makeRequest('get', $url, $data);
    }

    public function getProjectCustomFields($companyId)
    {
        $url = $this->getUrl("companies/{$companyId}/setup/customFields/projects/");

        return $this->makeRequest('get', $url, [
            'ShowFor.Quotes' => 'true'
        ]);
    }

    public function getProjectTags($companyId)
    {
        $url = $this->getUrl("companies/{$companyId}/setup/tags/projects/");

        return $this->makeRequest('get', $url);
    }

    public function getAll($pageSize, $collback)
    {
        $allResults = [];
        $page = 1;

        do {
            $result = $collback($pageSize, $page);

            $allResults = array_merge($allResults, $result);

            $page++;
        } while (count($result) === $pageSize);

        return $allResults;
    }

    protected function makeRequest($method, $url, $data = null, $headers = null)
    {
        $headers = empty($headers) ? $this->getHeaders() : $headers;

        $requestData = ($method === 'delete') ? $headers : $data;
        $method = "send{$method}";

        $response = $this->httpRequestService->$method($url, $requestData, $headers);

        return $this->httpRequestService->parseJsonResponse($response);
    }

    protected function getUrl($action)
    {
        return config('services.simpro.api_url') . "api/v1.0/{$action}";
    }

    protected function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('services.simpro.token'),
        ];
    }
}