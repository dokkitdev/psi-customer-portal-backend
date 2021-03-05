<?php

namespace App\Tests;

use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class JobAttachmentTest extends TestCase
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

    public function testDownloadJobAttachment()
    {
        $this->mockDownloadJobAttachment();

        Storage::put('8EgYd8urKKzzqcdDTKsKcpW9xWHxtwsKSoCscR3R7g4', 'content');

        $response = $this->actingAs($this->user)->json('get', '/job-attachments/download/1');

        $response->assertStatus(Response::HTTP_OK);

        Storage::delete('8EgYd8urKKzzqcdDTKsKcpW9xWHxtwsKSoCscR3R7g4');
    }

    public function testDownloadJobAttachmentNotExists()
    {
        $response = $this->actingAs($this->user)->json('get', '/job-attachments/download/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDownloadJobAttachmentNoAuth()
    {
        $response = $this->json('get', '/job-attachments/download/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
