<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SimproSitesTableAddCountyField extends Migration
{
    public function up()
    {
        Schema::table('simpro_sites', function (Blueprint $table) {
            $table->string('county')->nullable();
        });
    }

    public function down()
    {
        Schema::table('simpro_sites', function (Blueprint $table) {
            $table->dropColumn('county');
        });
    }
}
