<?php

namespace App\Tests;

use App\Models\GroupSimproSite;
use App\Models\SimproJob;
use App\Models\SimproSite;
use App\Models\SiteContact;
use App\Models\SiteCustomField;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Symfony\Component\HttpFoundation\Response;

class SimproSiteTest extends TestCase
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

    public function testUpdateSiteEvent()
    {
        $this->mockCreateOrUpdateSite();

        $this->createSimproJob('simpro_webhook_site_updated_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJob = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJob);

        $simproSite = SimproSite::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_site_create_or_update_event_fixture.json', $simproSite);

        $siteCustomFields = SiteCustomField::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_custom_fields_create_or_update_event_fixture.json', $siteCustomFields);

        $siteContacts = SiteContact::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_contacts_create_or_update_event_fixture.json', $siteContacts);

        $groupSimproSites = GroupSimproSite::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('group_simpro_sites_create_or_update_event_fixture.json', $groupSimproSites);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_simpro_sites.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_simpro_sites.json'
            ],
            [
                'filter' => ['query' => 'Name 1'],
                'result' => 'search_simpro_sites_by_query.json'
            ],
            [
                'filter' => [
                    'group_id' => 4,
                    'with' => ['group_simpro_sites']
                ],
                'result' => 'search_simpro_sites_by_group.json'
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
        $response = $this->actingAs($this->user)->json('get', '/simpro-sites', $filter);

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
        $response = $this->actingAs($this->admin)->json('get', '/simpro-sites', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture("admin_$fixture", $response->json());
    }
}
