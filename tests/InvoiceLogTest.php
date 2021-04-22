<?php

namespace App\Tests;

use App\Models\Invoice;
use App\Models\InvoiceLog;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class InvoiceLogTest extends TestCase
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

    public function testGetInvoicesToLogCommand()
    {
        $this->mockGetInvoices();

        $this->artisan('simpro:save-invoices-to-log')->assertExitCode(0);

        $invoiceLogs = InvoiceLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('invoice_logs_fixture.json', $invoiceLogs);
    }

    public function testHandleInvoicesLogCommand()
    {
        $this->mockCreateOrUpdateInvoice();

        $this->artisan('invoices-log:handle')->assertExitCode(0);

        $invoiceLogs = InvoiceLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('invoice_log_create_or_update_event_fixture.json', $invoiceLogs);

        $invoices = Invoice::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('invoice_create_or_update_event_fixture.json', $invoices);
    }
}
