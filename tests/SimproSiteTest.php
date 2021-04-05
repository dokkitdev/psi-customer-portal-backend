<?php

namespace App\Tests;

use App\Models\GroupSimproSite;
use App\Models\SimproJob;
use App\Models\SimproSite;
use App\Models\SiteContact;
use App\Models\SiteCustomField;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Illuminate\Support\Arr;
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

    public function testDeleteSiteEvent()
    {
        $this->createSimproJob('simpro_webhook_site_deleted_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJobs = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJobs);

        $this->assertDatabaseMissing('simpro_sites', ['id' => 5]);

        $this->assertDatabaseMissing('site_custom_fields', ['simpro_site_id' => 5]);

        $this->assertDatabaseMissing('site_contacts', ['simpro_site_id' => 5]);

        $this->assertDatabaseMissing('group_simpro_site', ['simpro_site_id' => 5]);
    }

    public function testGet()
    {
        $response = $this->actingAs($this->user)->json('get', '/simpro-sites/1', [
            'with' => ['group_simpro_sites', 'simpro_customer', 'site_custom_fields', 'site_contacts', 'primary_site_contact', 'reference_site_custom_field', 'customer_ref_site_custom_field'],
            'with_count' => ['open_jobs']
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_simpro_site_fixture.json', $response->json());
    }

    public function testGetNoPermission()
    {
        $response = $this->actingAs($this->user)->json('get', '/simpro-sites/4');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetByAdmin()
    {
        $response = $this->actingAs($this->admin)->json('get', '/simpro-sites/4');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_simpro_site_by_admin_fixture.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->user)->json('get', '/simpro-sites/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/simpro-sites/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
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
                    'with' => ['group_simpro_sites', 'simpro_customer', 'site_custom_fields', 'site_contacts', 'primary_site_contact', 'reference_site_custom_field', 'customer_ref_site_custom_field'],
                    'with_count' => ['open_jobs']
                ],
                'result' => 'search_simpro_sites_by_group.json'
            ],
            [
                'filter' => [
                    'customer_ref' => '100',
                    'has_open_jobs' => true,
                    'query' => 'name',
                    'with_count' => ['open_jobs']
                ],
                'result' => 'search_simpro_sites_by_complex.json'
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

    public function testUpdate()
    {
        $this->mockUpdateSite();

        $data = $this->getJsonFixture('update_simpro_site.json');

        $response = $this->actingAs($this->user)->json('put', '/simpro-sites/1', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('simpro_sites', Arr::except($data, ['primary_site_contact_id', 'site_custom_fields']));

        $siteContacts = SiteContact::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('update_primary_site_contact_fixture.json', $siteContacts);

        foreach ($data['site_custom_fields'] as $customField) {
            $this->assertDatabaseHas('site_custom_fields', $customField);
        }
    }

    public function testUpdateNoPermission()
    {
        $data = $this->getJsonFixture('update_simpro_site.json');

        $response = $this->actingAs($this->user)->json('put', '/simpro-sites/4', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdatePrimarySiteContactNotExists()
    {
        $data = $this->getJsonFixture('update_simpro_site.json');

        $data['primary_site_contact_id'] = 2;

        $response = $this->actingAs($this->user)->json('put', '/simpro-sites/1', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateCustomFieldNotExists()
    {
        $data = $this->getJsonFixture('update_simpro_site.json');

        $data['site_custom_fields'] = [
            [
                'id' => 2,
                'value' => 'some value...'
            ]
        ];

        $response = $this->actingAs($this->user)->json('put', '/simpro-sites/1', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateByAdmin()
    {
        $this->mockUpdateSiteByAdmin();

        $data = $this->getJsonFixture('update_simpro_site.json');

        $data['primary_site_contact_id'] = 3;
        $data['site_custom_fields'] = [
            [
                'id' => 4,
                'value' => 'some value...'
            ]
        ];

        $response = $this->actingAs($this->admin)->json('put', '/simpro-sites/4', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('simpro_sites', Arr::except($data, ['primary_site_contact_id', 'site_custom_fields']));

        $siteContacts = SiteContact::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('update_primary_site_contact_by_admin_fixture.json', $siteContacts);

        foreach ($data['site_custom_fields'] as $customField) {
            $this->assertDatabaseHas('site_custom_fields', $customField);
        }
    }

    public function testUpdateNotExists()
    {
        $data = $this->getJsonFixture('update_simpro_site.json');

        $response = $this->actingAs($this->user)->json('put', '/simpro-sites/0', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateNoAuth()
    {
        $data = $this->getJsonFixture('update_simpro_site.json');

        $response = $this->json('put', '/simpro-sites/1', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
