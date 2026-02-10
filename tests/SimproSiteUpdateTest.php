<?php

namespace App\Tests;

use App\Models\GroupSimproSite;
use App\Models\SimproSite;
use App\Models\SiteContact;
use App\Models\SiteCustomField;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class SimproSiteUpdateTest extends TestCase
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

    public function testUpdateSimproSitesCommand()
    {
        $this->mockUpdateSitesCommand();

        $this->artisan('simpro:update-sites')->assertExitCode(0);

        $sites = SimproSite::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_sites_update_fixture.json', $sites);

        $siteCustomFields = SiteCustomField::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_custom_fields_update_fixture.json', $siteCustomFields);

        $siteContacts = SiteContact::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_contacts_update_fixture.json', $siteContacts);

        $groupSimproSites = GroupSimproSite::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('group_simpro_sites_update_fixture.json', $groupSimproSites);
    }
}
