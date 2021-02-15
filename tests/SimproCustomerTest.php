<?php

namespace App\Tests;

use App\Models\SimproCustomer;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Symfony\Component\HttpFoundation\Response;

class SimproCustomerTest extends TestCase
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

    public function testGetCustomersCommand()
    {
        $this->mockGetCustomersCommand();

        $this->artisan('simpro:get-customers')->assertExitCode(0);

        $simproCustomers = SimproCustomer::orderBy('id')->get()->toArray();

        $this->assertEqualsFixture('simpro_customers_fixture.json', $simproCustomers);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_simpro_customers.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_simpro_customers.json'
            ],
            [
                'filter' => ['query' => '18 Hyde Park'],
                'result' => 'search_simpro_customers_by_query.json'
            ],
            [
                'filter' => ['has_groups' => 1],
                'result' => 'search_simpro_customers_with_groups_only.json'
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
        $response = $this->actingAs($this->admin)->json('get', '/simpro-customers', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }
}
