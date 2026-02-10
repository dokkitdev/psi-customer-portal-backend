<?php

namespace App\Tests;

use App\Models\Asset;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\JobAttachment;
use App\Models\JobCatalog;
use App\Models\JobLog;
use App\Models\JobWorkOrder;
use App\Models\Schedule;
use App\Models\SimproCustomer;
use App\Models\SimproSite;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class JobLogTest extends TestCase
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

    public function testGetJobsToLogCommand()
    {
        $this->mockGetJobs();

        $this->artisan('simpro:get-jobs-to-log')->assertExitCode(0);

        $jobLogs = JobLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('job_logs_fixture.json', $jobLogs);
    }

    public function testHandleJobLogCommand()
    {
        $this->mockCreateOrUpdateJob();

        $this->artisan('jobs-log:handle')->assertExitCode(0);

        $jobLogs = JobLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('job_log_create_or_update_event_fixture.json', $jobLogs);

        $job = Job::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('job_create_or_update_event_fixture.json', $job);

        $simproCustomer = SimproCustomer::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_customers_create_or_update_event_fixture.json', $simproCustomer);

        $simproSite = SimproSite::orderBy('id')->with(['site_custom_fields', 'site_contacts'])->get()->toArray();
        $this->assertEqualsFixture('simpro_site_create_or_update_event_fixture.json', $simproSite);

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
}
