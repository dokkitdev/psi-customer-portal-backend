<?php

namespace App\Tests;

use App\Models\Group;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Symfony\Component\HttpFoundation\Response;

class GroupTest extends TestCase
{
    use SimproTestTrait;

    protected $admin;
    protected $user;
    protected $data = ['title' => 'Group 6', 'simpro_customer_id' => 1];

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
    }

    public function testCreate()
    {
        $this->mockGetGroupSites();

        $response = $this->actingAs($this->admin)->json('post', '/groups', $this->data);

        $response->assertStatus(Response::HTTP_CREATED);

        $responseData = $response->json();

        $group = Group::with(['group_simpro_sites.simpro_site'])->find($responseData['id'])->toArray();

        $this->assertEqualsFixture('create_group_fixture.json', $group);
    }

    public function testCreateAlreadyExists()
    {
        $this->data['title'] = 'Group 1';

        $response = $this->actingAs($this->admin)->json('post', '/groups', $this->data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseHas('groups', [
            'title' => $this->data['title'],
            'simpro_customer_id' => $this->data['simpro_customer_id'],
        ]);
    }

    public function testCreateNoPermission()
    {
        $response = $this->actingAs($this->user)->json('post', '/groups', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCreateNoAuth()
    {
        $response = $this->json('post', '/groups', $this->data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdate()
    {
        $this->mockGetGroupSites();

        $this->data['simpro_customer_id'] = 2;

        $response = $this->actingAs($this->admin)->json('put', '/groups/1', $this->data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $group = Group::with(['group_simpro_sites.simpro_site'])->find(1)->toArray();

        $this->assertEqualsFixture('update_group_fixture.json', $group);
    }

    public function testUpdateSameCustomer()
    {
        $this->data['is_enabled_all_sites'] = false;

        $response = $this->actingAs($this->admin)->json('put', '/groups/1', $this->data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $group = Group::with(['group_simpro_sites.simpro_site'])->find(1)->toArray();

        $this->assertEqualsFixture('update_group_same_customer_fixture.json', $group);
    }

    public function testUpdateAlreadyExists()
    {
        $this->data['title'] = 'Group 2';

        $response = $this->actingAs($this->admin)->json('put', '/groups/1', $this->data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseHas('groups', [
            'id' => 2,
            'title' => $this->data['title']
        ]);
    }

    public function testUpdateNotExists()
    {
        $response = $this->actingAs($this->admin)->json('put', '/groups/0', $this->data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $this->assertDatabaseMissing('groups', [
            'id' => 0
        ]);
    }

    public function testUpdateNoPermission()
    {
        $response = $this->actingAs($this->user)->json('put', '/groups/1', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('groups', [
            'id' => 1,
            'title' => $this->data['title']
        ]);
    }

    public function testUpdateNoAuth()
    {
        $response = $this->json('put', '/groups/1', $this->data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseMissing('groups', [
            'id' => 1,
            'title' => $this->data['title']
        ]);
    }

    public function testDelete()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/groups/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('groups', [
            'id' => 1
        ]);
    }

    public function testDeleteNotExists()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/groups/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $this->assertDatabaseMissing('groups', [
            'id' => 0
        ]);
    }

    public function testDeleteNoPermission()
    {
        $response = $this->actingAs($this->user)->json('delete', '/groups/1');

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('groups', [
            'id' => 1
        ]);
    }

    public function testDeleteNoAuth()
    {
        $response = $this->json('delete', '/groups/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseHas('groups', [
            'id' => 1
        ]);
    }

    public function testGet()
    {
        $response = $this->actingAs($this->admin)->json('get', '/groups/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('group_fixture.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->admin)->json('get', '/groups/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetNoPermission()
    {
        $response = $this->actingAs($this->user)->json('get', '/groups/1');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/groups/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_groups.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_groups.json'
            ],
            [
                'filter' => ['query' => 'Group 4'],
                'result' => 'search_groups_by_query.json'
            ],
            [
                'filter' => ['simpro_customer_id' => 1],
                'result' => 'search_groups_by_simpro_customer.json'
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
        $response = $this->actingAs($this->admin)->json('get', '/groups', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }
}
