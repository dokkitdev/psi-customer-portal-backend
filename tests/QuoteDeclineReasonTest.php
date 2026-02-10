<?php

namespace App\Tests;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class QuoteDeclineReasonTest extends TestCase
{
    protected $admin;
    protected $user;
    protected $data = ['title' => 'Reason 5'];

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
    }

    public function testCreate()
    {
        $response = $this->actingAs($this->admin)->json('post', '/quote-decline-reasons', $this->data);

        $response->assertStatus(Response::HTTP_CREATED);

        $responseData = $response->json();

        $this->assertDatabaseHas('quote_decline_reasons', [
            'id' => $responseData['id'],
            'title' => $this->data['title'],
        ]);
    }

    public function testCreateNoPermission()
    {
        $response = $this->actingAs($this->user)->json('post', '/quote-decline-reasons', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCreateNoAuth()
    {
        $response = $this->json('post', '/quote-decline-reasons', $this->data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDelete()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/quote-decline-reasons/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('quote_decline_reasons', [
            'id' => 1
        ]);
    }

    public function testDeleteNotExists()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/quote-decline-reasons/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $this->assertDatabaseMissing('media', [
            'id' => 0
        ]);
    }

    public function testDeleteNoPermission()
    {
        $response = $this->actingAs($this->user)->json('delete', '/quote-decline-reasons/1');

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('quote_decline_reasons', [
            'id' => 1
        ]);
    }

    public function testDeleteNoAuth()
    {
        $response = $this->json('delete', '/quote-decline-reasons/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseHas('quote_decline_reasons', [
            'id' => 1
        ]);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_quote_decline_reasons.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_quote_decline_reasons.json'
            ],
            [
                'filter' => ['query' => 'Reason 4'],
                'result' => 'search_quote_decline_reasons_by_query.json'
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
        $response = $this->actingAs($this->admin)->json('get', '/quote-decline-reasons', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }
}
