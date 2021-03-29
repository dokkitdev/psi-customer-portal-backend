<?php

namespace App\Tests;

use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class QuoteTest extends TestCase
{
    use SimproTestTrait;

    protected $admin;
    protected $user;
    protected $files;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
        $this->files = [
            UploadedFile::fake()->image('file1.png', 600, 600),
            UploadedFile::fake()->image('file2.png', 600, 600)
        ];
    }

    public function testCreateRequest()
    {
        $this->mockCreateQuoteRequest();

        $response = $this->actingAs($this->user)->json('post', '/quotes/create-in-simpro', [
            'simpro_site_id' => 1,
            'type' => 1,
            'description' => 'Test quote...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testCreateRequestByAdmin()
    {
        $this->mockCreateQuoteRequest();

        $response = $this->actingAs($this->admin)->json('post', '/quotes/create-in-simpro', [
            'simpro_site_id' => 2,
            'type' => 2,
            'description' => 'Test quote...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testCreateRequestNoPermissions()
    {
        $response = $this->actingAs($this->user)->json('post', '/quotes/create-in-simpro', [
            'simpro_site_id' => 2,
            'type' => 1,
            'description' => 'Test quote...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCreateRequestSiteNotExists()
    {
        $response = $this->actingAs($this->admin)->json('post', '/quotes/create-in-simpro', [
            'simpro_site_id' => 0,
            'type' => 1,
            'description' => 'Test quote...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCreateRequestNoAuth()
    {
        $response = $this->json('post', '/quotes/create-in-simpro', [
            'simpro_site_id' => 1,
            'type' => 1,
            'description' => 'Test quote...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
