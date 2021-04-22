<?php

namespace App\Tests;

use App\Models\GroupSimproSite;
use App\Models\SimproSite;
use App\Models\SiteContact;
use App\Models\SiteCustomField;
use App\Models\SiteLog;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class SiteLogTest extends TestCase
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

    public function testGetSitesToLogCommand()
    {
        $this->mockGetSites();

        $this->artisan('simpro:save-sites-to-log')->assertExitCode(0);

        $siteLogs = SiteLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_logs_fixture.json', $siteLogs);
    }

    public function testHandleSitesLogCommand()
    {
        $this->mockCreateOrUpdateSite();

        $this->artisan('sites-log:handle')->assertExitCode(0);

        $siteLogs = SiteLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_log_create_or_update_event_fixture.json', $siteLogs);

        $simproSite = SimproSite::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_site_create_or_update_event_fixture.json', $simproSite);

        $siteCustomFields = SiteCustomField::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_custom_fields_create_or_update_event_fixture.json', $siteCustomFields);

        $siteContacts = SiteContact::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_contacts_create_or_update_event_fixture.json', $siteContacts);

        $groupSimproSites = GroupSimproSite::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('group_simpro_sites_create_or_update_event_fixture.json', $groupSimproSites);
    }
}
