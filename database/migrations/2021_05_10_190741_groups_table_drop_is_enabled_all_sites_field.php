<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GroupsTableDropIsEnabledAllSitesField extends Migration
{
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('is_enabled_all_sites');
        });
    }

    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('is_enabled_all_sites')->default(true);
        });
    }
}
