<?php

namespace App\Tests;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class QuoteStatusCodeTest extends TestCase
{
    protected $admin;
    protected $user;
    protected $data = ['status' => 'New'];

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
    }

    public function testUpdate()
    {
        $response = $this->actingAs($this->admin)->json('put', '/quote-status-codes/1', $this->data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->data['id'] = 1;
        $this->data['stage'] = 'In Progress';

        $this->assertDatabaseHas('quote_status_codes', $this->data);
    }

    public function testUpdateNotExists()
    {
        $response = $this->actingAs($this->admin)->json('put', '/quote-status-codes/0', $this->data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateNoPermission()
    {
        $response = $this->actingAs($this->user)->json('put', '/quote-status-codes/1', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateNoAuth()
    {
        $response = $this->json('put', '/quote-status-codes/1', $this->data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGet()
    {
        $response = $this->actingAs($this->user)->json('get', '/quote-status-codes/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_quote_status_code_fixture.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->user)->json('get', '/quote-status-codes/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/quote-status-codes/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_quote_status_codes.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_quote_status_codes.json'
            ],
            [
                'filter' => ['query' => 'Quote : On Hold'],
                'result' => 'search_quote_status_codes_by_query.json'
            ],
            [
                'filter' => ['status' => 'Pending'],
                'result' => 'search_quote_status_codes_by_status.json'
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
        $response = $this->actingAs($this->user)->json('get', '/quote-status-codes', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }
}
