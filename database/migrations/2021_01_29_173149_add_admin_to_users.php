<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAdminToUsers extends Migration
{
    public function up()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => '$2y$10$5gw6lHE84hmhJx7ECLlRmeXiEV3iZUrlHXDpcBAEsFfeW4NvS2n/W',
            'role_id' => 1
        ]);
    }

    public function down()
    {
        DB::table('users')
            ->where('email', '=', 'admin@admin.com')
            ->delete();
    }
}
