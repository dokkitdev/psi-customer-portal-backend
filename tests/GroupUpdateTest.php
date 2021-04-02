<?php

namespace App\Tests;

use App\Models\SimproSite;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class GroupUpdateTest extends TestCase
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

    public function testUpdateGroupsCommand()
    {
        $this->mockUpdateGroupsCommand();

        $this->artisan('simpro:update-groups')->assertExitCode(0);

        $simproSites = SimproSite::orderBy('id')->with(['group_simpro_sites'])->get()->toArray();
        $this->assertEqualsFixture('simpro_sites_update_fixture.json', $simproSites);
    }
}
