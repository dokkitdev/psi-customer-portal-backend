<?php

namespace App\Tests;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class GroupSimproSiteTest extends TestCase
{
    protected $admin;
    protected $user;
    protected $data = ['is_enabled' => false];

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
    }

    public function testUpdate()
    {
        $response = $this->actingAs($this->admin)->json('put', '/group-simpro-sites/1', $this->data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('group_simpro_site', [
            'id' => 1,
            'is_enabled' => $this->data['is_enabled']
        ]);
    }

    public function testUpdateNotExists()
    {
        $response = $this->actingAs($this->admin)->json('put', '/group-simpro-sites/0', $this->data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateNoPermission()
    {
        $response = $this->actingAs($this->user)->json('put', '/group-simpro-sites/1', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('group_simpro_site', [
            'id' => 1,
            'is_enabled' => $this->data['is_enabled']
        ]);
    }

    public function testUpdateNoAuth()
    {
        $response = $this->json('put', '/group-simpro-sites/1', $this->data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseMissing('group_simpro_site', [
            'id' => 1,
            'is_enabled' => $this->data['is_enabled']
        ]);
    }
}
