<?php

namespace App\Tests;

use App\Models\GroupSimproSite;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\JobAttachment;
use App\Models\JobCatalog;
use App\Models\JobWorkOrder;
use App\Models\Schedule;
use App\Models\SimproCustomer;
use App\Models\SimproJob;
use App\Models\SimproSite;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class JobTest extends TestCase
{
    use SimproTestTrait;

    protected $admin;
    protected $user;
    protected $files;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
        $this->files = [
            UploadedFile::fake()->image('file1.png', 600, 600),
            UploadedFile::fake()->image('file2.png', 600, 600)
        ];
    }

    public function testCreateJobEvent()
    {
        $this->mockCreateOrUpdateJob();

        $this->createSimproJob('simpro_webhook_job_created_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJob = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJob);

        $job = Job::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('job_create_or_update_event_fixture.json', $job);

        $simproCustomer = SimproCustomer::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_customers_create_or_update_event_fixture.json', $simproCustomer);

        $simproSite = SimproSite::orderBy('id')->with(['site_custom_fields', 'site_contacts'])->get()->toArray();
        $this->assertEqualsFixture('simpro_site_create_or_update_event_fixture.json', $simproSite);

        $groupSimproSites = GroupSimproSite::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('group_simpro_sites_create_or_update_event_fixture.json', $groupSimproSites);

        $schedules = Schedule::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('schedules_create_or_update_event_fixture.json', $schedules);

        $jobCatalogs = JobCatalog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('catalogs_create_or_update_event_fixture.json', $jobCatalogs);

        $jobAttachments = JobAttachment::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('attachments_create_or_update_event_fixture.json', $jobAttachments);

        $jobWorkOrders = JobWorkOrder::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('work_orders_create_or_update_event_fixture.json', $jobWorkOrders);

        $invoices = Invoice::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('invoices_create_or_update_event_fixture.json', $invoices);
    }

    public function testDeleteJobEvent()
    {
        $this->createSimproJob('simpro_webhook_job_deleted_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJobs = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJobs);

        $this->assertDatabaseMissing('jobs', ['id' => 1]);

        $this->assertDatabaseMissing('schedules', ['job_id' => 1]);

        $this->assertDatabaseMissing('job_catalogs', ['job_id' => 1]);

        $this->assertDatabaseMissing('job_attachments', ['job_id' => 1]);

        $this->assertDatabaseMissing('job_work_orders', ['job_id' => 1]);
    }

    public function testGet()
    {
        $response = $this->actingAs($this->user)->json('get', '/jobs/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_job_fixture.json', $response->json());
    }

    public function testGetNoPermission()
    {
        $response = $this->actingAs($this->user)->json('get', '/jobs/9');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetByAdmin()
    {
        $response = $this->actingAs($this->admin)->json('get', '/jobs/9');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_job_by_admin_fixture.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->user)->json('get', '/jobs/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/jobs/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_jobs.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_jobs.json'
            ],
            [
                'filter' => [
                    'query' => 'Sitename',
                ],
                'result' => 'search_by_query_jobs.json'
            ],
            [
                'filter' => [
                    'site_name' => 'Sitename 1',
                    'requested' => true,
                    'stage' => ['Progress'],
                    'appointment_from' => '2016-10-20 11:05:00',
                    'appointment_to' => '2016-10-20 11:05:00'
                ],
                'result' => 'search_by_complex_jobs.json'
            ],
            [
                'filter' => [
                    'start_time_from' => '2016-10-22 11:05:00',
                    'start_time_to' => '2016-10-18 11:05:00'
                ],
                'result' => 'search_by_time_jobs.json'
            ],
            [
                'filter' => [
                    'postal_code' => 'SL5 7HY',
                ],
                'result' => 'search_by_postal_code_jobs.json'
            ],
            [
                'filter' => [
                    'priority' => ['Fire Alarm - Standard', 'Intruder Alarm - Standard'],
                ],
                'result' => 'search_by_priority_jobs.json'
            ],
        ];
    }

    /**
     * @dataProvider  getSearchFilters
     *
     * @param  array $filter
     * @param  string $fixture
     */
    public function testSearch($filter, $fixture)
    {
        $response = $this->actingAs($this->user)->json('get', '/jobs', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }

    /**
     * @dataProvider  getSearchFilters
     *
     * @param  array $filter
     * @param  string $fixture
     */
    public function testSearchByAdmin($filter, $fixture)
    {
        $response = $this->actingAs($this->admin)->json('get', '/jobs', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture("admin_{$fixture}", $response->json());
    }

    public function testGetResponseTimes()
    {
        $this->mockGetResponseTimes();

        $response = $this->actingAs($this->user)->json('get', '/jobs/response-times');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_response_times_fixture.json', $response->json());
    }

    public function testGetResponseTimesNoAuth()
    {
        $response = $this->json('get', '/jobs/response-times');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetCostCenters()
    {
        $this->mockGetCostCenters();

        $response = $this->actingAs($this->user)->json('get', '/jobs/cost-centers');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_cost_centers_fixture.json', $response->json());
    }

    public function testGetCostCentersNoAuth()
    {
        $response = $this->json('get', '/jobs/cost-centers');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetBusinessGroups()
    {
        $this->mockGetBusinessGroups();

        $response = $this->actingAs($this->user)->json('get', '/jobs/business-groups');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_business_groups_fixture.json', $response->json());
    }

    public function testGetBusinessGroupsNoAuth()
    {
        $response = $this->json('get', '/jobs/business-groups');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateRequest()
    {
        $this->mockCreatejobRequest();

        $response = $this->actingAs($this->user)->json('post', '/jobs/create-in-simpro', [
            'simpro_site_id' => 1,
            'description' => 'Test job...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testCreateRequestByAdmin()
    {
        $this->mockCreatejobRequest();

        $response = $this->actingAs($this->admin)->json('post', '/jobs/create-in-simpro', [
            'simpro_site_id' => 2,
            'description' => 'Test job...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testCreateRequestNoPermissions()
    {
        $response = $this->actingAs($this->user)->json('post', '/jobs/create-in-simpro', [
            'simpro_site_id' => 2,
            'description' => 'Test job...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCreateRequestSiteNotExists()
    {
        $response = $this->actingAs($this->admin)->json('post', '/jobs/create-in-simpro', [
            'simpro_site_id' => 0,
            'description' => 'Test job...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCreateRequestNoAuth()
    {
        $response = $this->json('post', '/jobs/create-in-simpro', [
            'simpro_site_id' => 1,
            'description' => 'Test job...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
