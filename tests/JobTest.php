<?php

namespace App\Tests;

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
use Symfony\Component\HttpFoundation\Response;

class JobTest extends TestCase
{
    use SimproTestTrait;

    protected $admin;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
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

        $simproSite = SimproSite::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_site_create_or_update_event_fixture.json', $simproSite);

        $schedules = Schedule::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('schedules_create_or_update_event_fixture.json', $schedules);

        $jobWorkOrders = JobCatalog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('catalogs_create_or_update_event_fixture.json', $jobWorkOrders);

        $jobWorkOrders = JobAttachment::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('attachments_create_or_update_event_fixture.json', $jobWorkOrders);

        $jobWorkOrders = JobWorkOrder::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('work_orders_create_or_update_event_fixture.json', $jobWorkOrders);
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
}
