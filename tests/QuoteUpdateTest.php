<?php

namespace App\Tests;

use App\Models\Quote;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class QuoteUpdateTest extends TestCase
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

    public function testUpdateQuoteCommand()
    {
        $this->mockUpdateQuoteCommand();

        $this->artisan('simpro:update-quotes')->assertExitCode(0);

        $quotes = Quote::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('quotes_update_fixture.json', $quotes);
    }
}
