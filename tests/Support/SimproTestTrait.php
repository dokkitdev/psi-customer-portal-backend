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

    protected function mockApproveQuote()
    {
        $this->mockHttpRequestService([
            $this->patchQuoteRequest()
        ]);
    }

    protected function mockPostQuoteNote()
    {
        $this->mockHttpRequestService([
            $this->postQuoteNoteRequest()
        ]);
    }

    protected function postQuoteNoteRequest()
    {
        return [
            'type' => 'post',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52820/notes/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'post_quote_note_response_success.json'
            ]
        ];
    }

    protected function patchQuoteRequest()
    {
        return [
            'type' => 'patch',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52820'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'patch_quote_response_success.json'
            ]
        ];
    }

    protected function mockCreateOrUpdateQuote()
    {
        $this->mockHttpRequestService([
            $this->getQuote(),
            $this->getCustomer(),
            $this->getJob(),
            $this->getCustomer(),
            $this->getSite(),
            $this->getSiteContacts(),
            $this->getQuoteNote(),
            $this->getQuoteNoteAttachments()
        ]);
    }

    protected function mockUpdateGroupsCommand()
    {
        $this->mockHttpRequestService([
            $this->getSites(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSites(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
        ]);
    }

    protected function mockUpdateSitesCommand()
    {
        $this->mockHttpRequestService([
            $this->getSite(),
            $this->getSite(),
            $this->getCustomer(),
            $this->getSiteContacts(),
            $this->getSite(),
            $this->getSite(),
            $this->getSiteContacts(),
        ]);
    }

    protected function mockCreateQuoteRequest()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'post',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'post_quote_response_success.json'
                ]
            ],
            $this->mockPostQuoteAttachment(),
            $this->mockPostQuoteAttachment()
        ]);
    }

    protected function mockCreatejobRequest()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'post',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'post_job_response_success.json'
                ]
            ],
            $this->mockPostJobAttachment(),
            $this->mockPostJobAttachment()
        ]);
    }

    protected function getQuote()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52648'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_quote_response_success.json'
            ]
        ];
    }

    protected function getQuoteNote()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52648/notes/17256'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_quote_note_response_success.json'
            ]
        ];
    }

    protected function getQuoteNoteAttachments()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52648/notes/17256/attachments/files/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_quote_note_attachments_response_success.json'
            ]
        ];
    }

    protected function mockPostJobAttachment()
    {
        return [
            'type' => 'post',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/210600/attachments/files/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'post_job_attachment_response_success.json'
            ]
        ];
    }

    protected function mockPostQuoteAttachment()
    {
        return [
            'type' => 'post',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/quotes/52820/attachments/files/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'post_quote_attachment_response_success.json'
            ]
        ];
    }

    protected function mockPatchSiteContact()
    {
        $this->mockHttpRequestService([
            $this->patchSiteContact()
        ]);
    }

    protected function mockDeleteSiteContact()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'delete',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/3900/contacts/11026'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'delete_site_contact_response_success.json'
                ]
            ]
        ]);
    }

    protected function mockPostSiteContact()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'post',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/3900/contacts/'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'post_site_contact_response_success.json'
                ]
            ]
        ]);
    }

    protected function mockUpdateSite()
    {
        $this->mockHttpRequestService([
            $this->patchSite(),
            $this->patchSiteContact(),
            $this->patchSiteCustomField(),
            $this->patchSiteCustomField()
        ]);
    }

    protected function mockUpdateSiteByAdmin()
    {
        $this->mockHttpRequestService([
            $this->patchSite(),
            $this->patchSiteContact(),
            $this->patchSiteCustomField()
        ]);
    }

    protected function patchSite()
    {
        return [
            'type' => 'patch',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/3900'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'patch_site_response_success.json'
            ]
        ];
    }

    protected function patchSiteContact()
    {
        return [
            'type' => 'patch',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/3900/contacts/11026'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'patch_site_contact_response_success.json'
            ]
        ];
    }

    protected function patchSiteCustomField()
    {
        return [
            'type' => 'patch',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/3900/customFields/22'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'patch_site_custom_field_response_success.json'
            ]
        ];
    }

    protected function mockCreateOrUpdateSite()
    {
        $this->mockHttpRequestService([
            $this->getSite(),
            $this->getSiteContacts()
        ]);
    }

    protected function mockGetJobs()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'get',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'get_jobs_response_success.json'
                ]
            ]
        ]);
    }

    protected function mockUpdateJobsCommand()
    {
        $this->mockHttpRequestService([
            $this->getJob(),
            $this->getJob(),
        ]);
    }

    protected function mockDownloadJobAttachment()
    {
        $this->mockHttpRequestService([
            $this->getJobAttachmentFile(),
        ]);
    }

    protected function mockDownloadQuoteNoteAttachment()
    {
        $this->mockHttpRequestService([
            $this->getQuoteNoteAttachmentFile(),
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
            $this->getCustomer(),
            $this->getSite(),
            $this->getSiteContacts(),
            $this->getSchedules(),
            $this->getJobAttachments(),
            $this->getJobWorkOrders(),
            $this->getJobInvoices(),
            $this->getCustomerInvoice()
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

    protected function getQuoteNoteAttachmentFile()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://seville.simprosuite.com/api/v1.0/companies/0/quotes/52648/notes/17256/attachments/files/1n9nS2sI3NaTnu0kSDa_XpqOGMMm_VuQ_awG0Mn4E0g/view/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_quote_note_attachment_file_response_success.json'
            ]
        ];
    }

    protected function getJobInvoices()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/jobs/209000/invoices/'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_invoices_response_success.json'
            ]
        ];
    }

    protected function getCustomerInvoice()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/customerInvoices/103987'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_customer_invoice_response_success.json'
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

    protected function getSiteContacts()
    {
        return [
            'type' => 'get',
            'arguments' => [
                $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/sites/2406/contacts'),
                $this->equalTo(null),
                $this->equalTo([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer token',
                ])
            ],
            'response' => [
                'fixture' => 'get_site_contacts_response_success.json'
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
            $this->getSites(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
            $this->getSiteContacts(),
        ]);
    }

    protected function getSites()
    {
        return [
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
        ];
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

    protected function mockGetResponseTimes()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'get',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/setup/responseTimes'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'get_response_times_response_success.json'
                ]
            ]
        ]);
    }

    protected function mockGetCostCenters()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'get',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/setup/accounts/costCenters/'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'get_cost_centers_response_success.json'
                ]
            ]
        ]);
    }

    protected function mockGetBusinessGroups()
    {
        $this->mockHttpRequestService([
            [
                'type' => 'get',
                'arguments' => [
                    $this->equalTo('https://pfsgroup.simprosuite.com/api/v1.0/companies/0/setup/accounts/businessGroups/'),
                    $this->equalTo(null),
                    $this->equalTo([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer token',
                    ])
                ],
                'response' => [
                    'fixture' => 'get_business_groups_response_success.json'
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