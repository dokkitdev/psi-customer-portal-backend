<?php

namespace App\ApiClients;

use Illuminate\Support\Facades\Storage;
use RonasIT\Support\Services\HttpRequestService;

class SimproApiClient
{
    protected HttpRequestService $httpRequestService;

    public function __construct()
    {
        $this->httpRequestService = app(HttpRequestService::class);
    }

    public function getJobsAsGenerator($companyId)
    {
        $page = 1;
        $pageSize = 250;
        $url = $this->getUrl("companies/{$companyId}/jobs/");

        do {
            $result = $this->makeRequest('get', $url, [
                'page' => $page,
                'pageSize' => $pageSize,
            ]);

            $page++;

            yield $result;
        } while (count($result) === $pageSize);
    }

    public function getWorkOrders($companyId, $jobId, $sectionId, $costCenterId)
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}/sections/{$sectionId}/costCenters/{$costCenterId}/workOrders/");

        return $this->makeRequest('get', $url, [
            'columns' => 'ID,Staff,DescriptionNotes,WorkOrderDate',
            'pageSize' => 250
        ]);
    }

    public function downloadJobAttachment($companyId, $jobId, $attachmentId)
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}/attachments/files/{$attachmentId}/view/");

        return $this->downloadAttachment($url, $attachmentId);
    }

    public function getPublicJobAttachmentsAsGenerator($companyId, $jobId)
    {
        $page = 1;
        $pageSize = 250;
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}/attachments/files/");

        do {
            $result = $this->makeRequest('get', $url, [
                'page' => $page,
                'pageSize' => $pageSize,
                'columns' => 'ID,Filename,Public',
                'Public' => 'true'
            ]);

            $page++;

            yield $result;
        } while (count($result) === $pageSize);
    }

    public function postJobAttachment($companyId, $jobId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/$jobId/attachments/files/");

        return $this->makeRequest('post', $url, $data);
    }

    public function postQuoteAttachment($companyId, $quoteId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/$quoteId/attachments/files/");

        return $this->makeRequest('post', $url, $data);
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

    public function patchSite($companyId, $siteId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function getSiteContacts($companyId, $siteId)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/");

        return $this->makeRequest('get', $url, [
            'pageSize' => 250,
            'columns' => 'ID,Title,GivenName,FamilyName,Email,WorkPhone,CellPhone,Position,PrimaryContact'
        ]);
    }

    public function postSiteContact($companyId, $siteId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/");

        return $this->makeRequest('post', $url, $data);
    }

    public function patchSiteContact($companyId, $siteId, $contactId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/$contactId");

        return $this->makeRequest('patch', $url, $data);
    }

    public function deleteSiteContact($companyId, $siteId, $contactId)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/$contactId");

        return $this->makeRequest('delete', $url);
    }

    public function patchSiteCustomField($companyId, $siteId, $customFieldId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/customFields/$customFieldId");

        return $this->makeRequest('patch', $url, $data);
    }

    public function getJob($companyId, $jobId)
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}");

        return $this->makeRequest('get', $url, [
            'display' => 'all'
        ]);
    }

    public function postJob($companyId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/");

        return $this->makeRequest('post', $url, $data);
    }

    public function postQuote($companyId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/");

        return $this->makeRequest('post', $url, $data);
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

    public function getResponseTimesAsGenerator($companyId)
    {
        $page = 1;
        $pageSize = 250;
        $url = $this->getUrl("companies/{$companyId}/setup/responseTimes/");

        do {
            $result = $this->makeRequest('get', $url, [
                'page' => $page,
                'pageSize' => $pageSize,
            ]);

            $page++;

            yield $result;
        } while (count($result) === $pageSize);
    }

    public function getCostCentersAsGenerator($companyId)
    {
        $page = 1;
        $pageSize = 250;
        $url = $this->getUrl("companies/{$companyId}/setup/accounts/costCenters/");

        do {
            $result = $this->makeRequest('get', $url, [
                'page' => $page,
                'pageSize' => $pageSize,
            ]);

            $page++;

            yield $result;
        } while (count($result) === $pageSize);
    }

    public function getBusinessGroupsAsGenerator($companyId)
    {
        $page = 1;
        $pageSize = 250;
        $url = $this->getUrl("companies/{$companyId}/setup/accounts/businessGroups/");

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

    protected function downloadAttachment($url, $attachmentId)
    {
        $this->httpRequestService->set('sink', Storage::path($attachmentId));
        $response = $this->httpRequestService->sendGet($url, null, $this->getHeaders());

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