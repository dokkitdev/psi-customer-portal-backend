<?php

namespace App\Tests;

use App\Models\User;
use App\Tests\Support\MockHttpRequestServiceTrait;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class JobAttachmentTest extends TestCase
{
    use MockHttpRequestServiceTrait;

    protected array $requiredOriginStates = [
        'job_attachments',
        'simpro_jobs',
    ];

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::find(2);

        Storage::fake();
    }

    /**
     * @testCase download_attachment
     */
    public function testDownloadJobAttachment(): void
    {
        $this->mockHttpRequestService('requests_chain.json');

        Storage::put('8EgYd8urKKzzqcdDTKsKcpW9xWHxtwsKSoCscR3R7g4', 'previously_downloaded_content');

        $response = $this->actingAs($this->user)->json('get', '/job-attachments/download/1');

        $this->assertEquals('actual_downloaded_content', Storage::get('8EgYd8urKKzzqcdDTKsKcpW9xWHxtwsKSoCscR3R7g4'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Disposition', 'inline; filename=Jobcard_For_Job_No_209000_24-02-2021_0942.pdf');

        ob_start();
        $response->sendContent();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('actual_downloaded_content', $content);
    }

    public function testDownloadJobAttachmentNotExists(): void
    {
        $response = $this->actingAs($this->user)->json('get', '/job-attachments/download/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $this->assertEmpty(Storage::files());
    }

    public function testDownloadJobAttachmentNoAuth(): void
    {
        $response = $this->json('get', '/job-attachments/download/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertEmpty(Storage::files());
    }

    public function getTestHandleWebhookData(): array
    {
        return [
            ['handle_webhook__job_attachment_created_or_updated__no_local_job'],
            ['handle_webhook__job_attachment_created_or_updated__attachment_not_public'],
            ['handle_webhook__job_attachment_created_or_updated__attachment_created'],
            ['handle_webhook__job_attachment_created_or_updated__attachment_updated'],
            ['handle_webhook__job_attachment_deleted__no_local_job'],
            ['handle_webhook__job_attachment_deleted__no_local_attachment'],
            ['handle_webhook__job_attachment_deleted__attachment_deleted'],
        ];
    }

    /**
     * @dataProvider getTestHandleWebhookData
     * @providedTestCase
     */
    public function testHandleWebhook(): void
    {
        if (file_exists($this->getFixturePath('requests_chain.json'))) {
            $this->mockHttpRequestService('requests_chain.json');
        }

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $this->assertChangesEqualsFixture('job_attachments');
        $this->assertChangesEqualsFixture('simpro_jobs');
    }
}
