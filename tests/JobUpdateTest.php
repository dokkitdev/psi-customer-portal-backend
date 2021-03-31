<?php

namespace App\Tests;

use App\Models\Job;
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
    }
}
