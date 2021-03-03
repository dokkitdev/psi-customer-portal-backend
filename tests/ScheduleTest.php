<?php

namespace App\Tests;

use App\Models\Schedule;
use App\Models\SimproJob;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class ScheduleTest extends TestCase
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

    public function testCreateScheduleEvent()
    {
        $this->mockCreateOrUpdateSchedule();

        $this->createSimproJob('simpro_webhook_schedule_created_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJob = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJob);

        $schedule = Schedule::orderBy('id')->where('id', 2)->get()->toArray();
        $this->assertEqualsFixture('schedule_create_or_update_event_fixture.json', $schedule);

        $this->assertDatabaseHas('jobs', [
            'id' => 1,
            'recent_schedule_id' => 2
        ]);
    }

    public function testDeleteScheduleEvent()
    {
        $this->createSimproJob('simpro_webhook_schedule_deleted_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJobs = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJobs);

        $this->assertDatabaseMissing('schedules', ['id' => 1]);

        $this->assertDatabaseHas('jobs', [
            'id' => 1,
            'recent_schedule_id' => null
        ]);
    }
}
