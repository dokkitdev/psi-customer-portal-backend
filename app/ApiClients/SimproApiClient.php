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

    public function getCustomersAsGenerator($companyId, $type)
    {
        $page = 1;
        $pageSize = 250;
        $url = $this->getUrl("companies/{$companyId}/customers/{$type}/");

        do {
            $result = $this->makeRequest('get', $url, [
                'page' => $page,
                'pageSize' => $pageSize
            ]);

            $page++;

            yield $result;
        } while (count($result) === $pageSize);
    }

    public function getSitesAsGenerator($companyId, $customerId)
    {
        $page = 1;
        $pageSize = 250;
        $url = $this->getUrl("companies/{$companyId}/sites/");

        do {
            $result = $this->makeRequest('get', $url, [
                'page' => $page,
                'pageSize' => $pageSize,
                'Customers.ID' => $customerId
            ]);

            $page++;

            yield $result;
        } while (count($result) === $pageSize);
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