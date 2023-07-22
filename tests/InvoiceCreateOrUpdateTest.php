<?php

namespace App\Tests;

use App\Tests\Support\MockHttpRequestServiceTrait;

class InvoiceCreateOrUpdateTest extends TestCase
{
    use MockHttpRequestServiceTrait;

    protected array $requiredOriginStates = [
        'invoices',
        'simpro_jobs',
        'job_attachments',
    ];

    public function getTestCreateOrUpdateByJobWebhookData(): array
    {
        return [
            ['create_or_update__by_job_webhook/no_invoices_simpro__no_invoices_local__check_nothing_changed'],
            ['create_or_update__by_job_webhook/no_invoices_simpro__exist_invoices_local__check_all_local_deleted'],
            ['create_or_update__by_job_webhook/exist_invoices_simpro__exist_invoices_local__check_mixed_created_deleted_updated'],
            ['create_or_update__by_job_webhook/check_job_attachment_id_selecting__no_job_attachments'],
            ['create_or_update__by_job_webhook/check_job_attachment_id_selecting__no_suitable_job_attachments'],
            ['create_or_update__by_job_webhook/check_job_attachment_id_selecting__found_job_attachment'],
        ];
    }

    /**
     * @dataProvider getTestCreateOrUpdateByJobWebhookData
     * @providedTestCase
     */
    public function testCreateOrUpdateByJobWebhook(): void
    {
        $this->mockHttpRequestService('requests_chain.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $this->assertChangesEqualsFixture('invoices');
        $this->assertChangesEqualsFixture('simpro_jobs');
        $this->assertChangesEqualsFixture('job_attachments');
    }
}
