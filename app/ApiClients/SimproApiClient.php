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

    public function getSchedule($companyId, $scheduleId)
    {
        $url = $this->getUrl("companies/{$companyId}/schedules/{$scheduleId}");

        return $this->makeRequest('get', $url);
    }

    public function getSchedulesAsGenerator($companyId, $jobId)
    {
        $page = 1;
        $pageSize = 250;
        $url = $this->getUrl("companies/{$companyId}/schedules/");

        do {
            $result = $this->makeRequest('get', $url, [
                'page' => $page,
                'pageSize' => $pageSize,
                'Reference' => "{$jobId}%",
            ]);

            $page++;

            yield $result;
        } while (count($result) === $pageSize);
    }

    public function getSite($companyId, $siteId)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}");

        return $this->makeRequest('get', $url);
    }

    public function getJob($companyId, $jobId)
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}");

        return $this->makeRequest('get', $url, [
            'display' => 'all'
        ]);
    }

    public function getCustomer($companyId, $type, $customerId)
    {
        $url = $this->getUrl("companies/{$companyId}/customers/{$type}/{$customerId}");

        return $this->makeRequest('get', $url);
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
                'Customers.ID' => $customerId,
                'columns' => 'ID,Name,Address'
            ]);

            $page++;

            yield $result;
        } while (count($result) === $pageSize);
    }

    public function getProjectCustomFieldsAsGenerator($companyId)
    {
        $page = 1;
        $pageSize = 250;
        $url = $this->getUrl("companies/{$companyId}/setup/customFields/projects/");

        do {
            $result = $this->makeRequest('get', $url, [
                'page' => $page,
                'pageSize' => $pageSize,
                'ShowFor.Quotes' => 'true'
            ]);

            $page++;

            yield $result;
        } while (count($result) === $pageSize);
    }

    public function getProjectTagsAsGenerator($companyId)
    {
        $page = 1;
        $pageSize = 250;
        $url = $this->getUrl("companies/{$companyId}/setup/tags/projects/");

        do {
            $result = $this->makeRequest('get', $url, [
                'page' => $page,
                'pageSize' => $pageSize,
            ]);

            $page++;

            yield $result;
        } while (count($result) === $pageSize);
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