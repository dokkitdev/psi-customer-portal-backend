<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDefaultSettings extends Migration
{
    public function up()
    {
        DB::table('settings')->insert([
            ['name' => 'default_tag', 'value' => '{"ID": 7, "Name": "Alex Grant-Browning"}', 'is_public' => true],
            ['name' => 'admin_email', 'value' => '{"email": "admin@example.com"}', 'is_public' => true],
            ['name' => 'quote_validity', 'value' => '{"value": 30}', 'is_public' => true],
            ['name' => 'quote_date_created', 'value' => '{"ID": 1, "Name": "Reason for Quote"}', 'is_public' => true],
        ]);
    }

    public function down()
    {
        DB::table('settings')
            ->whereIn('name', ['default_tag', 'admin_email', 'quote_validity', 'quote_date_created'])
            ->delete();
    }
}
