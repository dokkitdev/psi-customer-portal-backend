<?php

namespace App\Tests;

use App\Models\Asset;
use App\Models\SimproCustomer;
use App\Models\SimproJob;
use App\Models\SimproSite;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Symfony\Component\HttpFoundation\Response;

class AssetTest extends TestCase
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

    public function testUpdateAssetEvent()
    {
        $this->mockCreateOrUpdateAsset();

        $this->createSimproJob('simpro_webhook_asset_updated_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJob = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJob);

        $assets = Asset::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('asset_create_or_update_event_fixture.json', $assets);

        $simproCustomer = SimproCustomer::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_customer_create_or_update_event_fixture.json', $simproCustomer);

        $simproSite = SimproSite::orderBy('id')->with(['site_custom_fields', 'site_contacts'])->get()->toArray();
        $this->assertEqualsFixture('simpro_site_create_or_update_event_fixture.json', $simproSite);
    }

    public function testDeleteAssetEvent()
    {
        $this->createSimproJob('simpro_webhook_asset_deleted_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJob = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJob);

        $assets = Asset::orderBy('id')->get()->toArray();
        $this->exportJson('asset_delete_event_fixture.json', $assets);
    }

    public function testGet()
    {
        $response = $this->actingAs($this->user)->json('get', '/assets/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_asset_fixture.json', $response->json());
    }

    public function testGetNoPermission()
    {
        $response = $this->actingAs($this->user)->json('get', '/assets/9');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetByAdmin()
    {
        $response = $this->actingAs($this->admin)->json('get', '/assets/9');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_asset_by_admin_fixture.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->user)->json('get', '/assets/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/assets/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_assets.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_assets.json'
            ],
            [
                'filter' => [
                    'order_by' => 'simpro_site_id',
                    'desc' => false,
                    'query' => 'Name',
                    'type' => 'Child',
                    'parent_id' => 1,
                    'archived' => false,
                    'simpro_customer_id' => 3,
                    'simpro_site_id' => 1,
                    'last_test_date' => '2016-10-20',
                    'next_service_date' => '2016-10-20',
                    'last_test_result_query' => 'Test result',
                    'service_level_name_query' => 'Service level',
                    'asset_id' => 1
                ],
                'result' => 'search_assets_complex.json'
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
        $response = $this->actingAs($this->user)->json('get', '/assets', $filter);

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
        $response = $this->actingAs($this->admin)->json('get', '/assets', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture("admin_{$fixture}", $response->json());
    }
}
