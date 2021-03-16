<?php

namespace App\Tests;

use App\Models\Job;
use App\Models\JobWorkOrder;
use App\Models\Schedule;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class JobUpdateTest extends TestCase
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

    public function testUpdateJobsCommand()
    {
        $this->mockUpdateJobsCommand();

        $this->artisan('simpro:update-jobs')->assertExitCode(0);

        $jobs = Job::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('jobs_update_fixture.json', $jobs);

        $schedules = Schedule::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('schedules_update_fixture.json', $schedules);

        $jobWorkOrders = JobWorkOrder::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('work_orders_update_fixture.json', $jobWorkOrders);
    }
}
