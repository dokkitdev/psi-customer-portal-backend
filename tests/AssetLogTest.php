<?php

namespace App\Tests;

use App\Models\Asset;
use App\Models\AssetLog;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class AssetLogTest extends TestCase
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

    public function testGetAssetsToLogCommand()
    {
        $this->mockGetAssets();

        $this->artisan('simpro:save-assets-to-log')->assertExitCode(0);

        $assetLogs = AssetLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('asset_logs_fixture.json', $assetLogs);
    }

    public function testHandleAssetsLogCommand()
    {
        $this->mockCreateOrUpdateAsset();

        $this->artisan('assets-log:handle')->assertExitCode(0);

        $assetlogs = AssetLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('asset_log_create_or_update_event_fixture.json', $assetlogs);

        $assets = Asset::orderBy('id')->with([
            'simpro_site',
            'simpro_customer',
            'asset_custom_fields',
            'asset_attachments',
            'asset_test_records.job',
            'asset_test_records.asset_test_record_readings'
        ])->get()->toArray();
        $this->assertEqualsFixture('asset_create_or_update_event_fixture.json', $assets);
    }
}
