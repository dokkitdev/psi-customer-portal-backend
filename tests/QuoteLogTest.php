<?php

namespace App\Tests;

use App\Models\Job;
use App\Models\Quote;
use App\Models\QuoteLog;
use App\Models\SimproCustomer;
use App\Models\SimproSite;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class QuoteLogTest extends TestCase
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

    public function testGetQuotesToLogCommand()
    {
        $this->mockGetQuotes();

        $this->artisan('simpro:save-quotes-to-log')->assertExitCode(0);

        $quoteLogs = QuoteLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('quote_logs_fixture.json', $quoteLogs);
    }

    public function testHandleQuotesLogCommand()
    {
        $this->mockCreateOrUpdateQuote();

        $this->artisan('quotes-log:handle')->assertExitCode(0);

        $quoteLogs = QuoteLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('quote_log_create_or_update_event_fixture.json', $quoteLogs);

        $quotes = Quote::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('quote_create_or_update_event_fixture.json', $quotes);

        $simproCustomer = SimproCustomer::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_customer_create_or_update_event_fixture.json', $simproCustomer);

        $simproSite = SimproSite::orderBy('id')->with(['site_custom_fields', 'site_contacts'])->get()->toArray();
        $this->assertEqualsFixture('simpro_site_create_or_update_event_fixture.json', $simproSite);

        $jobs = Job::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('jobs_create_or_update_event_fixture.json', $jobs);
    }
}
