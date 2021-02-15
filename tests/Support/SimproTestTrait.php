<?php

namespace App\Tests\Support;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\HttpRequestService;

trait SimproTestTrait
{
    use MockClassTrait;

    protected function mockGetSites()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'get',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'get_sites_response_success.json'
                ]
            ]
        ]);
    }

    protected function mockGetCustomersCommand()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'get',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/customers/companies'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'get_company_customers_response_success.json'
                ]
            ],
            [
                'type' => 'get',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/customers/individuals'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'get_individual_customers_response_success.json'
                ]
            ]
        ]);
    }

    protected function mockGetProjectTags()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'get',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/setup/tags/projects'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'get_project_tags_response_success.json'
                ]
            ]
        ]);
    }

    protected function mockGetProjectCustomFields()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'get',
                'arguments' => [
                    $this->equalTo('https://seville.simprosuite.com/api/v1.0/companies/0/setup/customFields/projects'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'get_project_custom_fields_response_success.json'
                ]
            ]
        ]);
    }

    protected function mockHttpRequestService(array $requestsChain = [], $className = HttpRequestService::class)
    {
        $mock = $this->createHttpRequestServiceMock([], $className);

        foreach ($requestsChain as $index => $request) {
            $returnMethod = 'willReturn';

            $response = $this->makeResponse(Arr::get($request, 'response.fixture'));

            $mock
                ->expects($this->at($index))
                ->method("send{$request['type']}")
                ->$returnMethod($response);
        }

        $this->app->instance($className, $mock);
    }

    protected function makeResponse($fixture = null)
    {
        $response = is_array($fixture) ? json_encode($fixture) : $this->getJsonFixture($fixture, true);

        $status = $response['status'];
        $headers = $response['headers'];
        $response = $response['content'];

        return new GuzzleResponse($status, $headers, $response);
    }

    protected function createHttpRequestServiceMock($additionalMethods = [], $className = HttpRequestService::class)
    {
        $methods = array_merge($additionalMethods, [
            'sendGet',
            'sendPost',
            'sendPut',
            'sendDelete',
            'sendPatch'
        ]);

        return $this->getMockBuilder($className)->setMethods($methods)->getMock();
    }
}