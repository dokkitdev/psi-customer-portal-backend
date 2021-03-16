<?php

namespace App\Tests\Support;

use App\Models\SimproJob;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\HttpRequestService;

trait SimproTestTrait
{
    use MockClassTrait;

    protected function createSimproJob($fixture)
    {
        $webhookData = $this->getJsonFixture($fixture);
        $webhookData['data'] = json_decode($webhookData['data'], true);
        SimproJob::create($webhookData);
    }

    protected function mockUpdateJobsCommand()
    {
        $this->mockHttpRequestService([
            $this->getSchedules(),
            $this->getJob(),
            $this->getJobWorkOrders(),
            $this->getSchedules(),
            $this->getJob(),
            $this->getJobWorkOrders()
        ]);
    }

    protected function mockDownloadJobAttachment()
    {
        $this->mockHttpRequestService([
            $this->getJobAttachmentFile(),
        ]);
    }

    protected function mockCreateOrUpdateSchedule()
    {
        $this->mockHttpRequestService([
            $this->getSchedule()
        ]);
    }

    protected function mockCreateOrUpdateJob()
    {
        $this->mockHttpRequestService([
            $this->getJob(),
            $this->getSite(),
            $this->getCustomer(),
            $this->getSchedules(),
            $this->getJobAttachments(),
            $this->getJobWorkOrders()
        ]);
    }

    protected function mockGetCustomer()
    {
        $this->mockHttpRequestService([
            $this->getCustomer()
        ]);
    }

    protected function getJobAttachmentFile()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://seville.simprosuite.com/api/v1.0/companies/0/jobs/2406/attachments/files/7Dcva_XBYo8fqg1hOtixXE5jSFDabVFFdU6l5GdS2FI/view/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_job_attachment_file_response_success.json'
            ]
        ];
    }

    protected function getJobWorkOrders()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/209000/sections/9049/costCenters/9092/workOrders/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_job_work_orders_response_success.json'
            ]
        ];
    }

    protected function getJobAttachments()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/209000/attachments/files/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_job_attachments_response_success.json'
            ]
        ];
    }

    protected function getSchedules()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/schedules/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_schedules_response_success.json'
            ]
        ];
    }

    protected function getSchedule()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/schedules/18734'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_schedule_response_success.json'
            ]
        ];
    }

    protected function getJob()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/209000'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_job_response_success.json'
            ]
        ];
    }

    protected function getSite()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/2406'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_site_response_success.json'
            ]
        ];
    }

    protected function getCustomer()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/customers/companies/6'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_customer_response_success.json'
            ]
        ];
    }

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