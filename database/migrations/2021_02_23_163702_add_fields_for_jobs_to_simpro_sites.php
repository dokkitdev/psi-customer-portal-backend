<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsForJobsToSimproSites extends Migration
{
    public function up()
    {
        Schema::table('simpro_sites', function (Blueprint $table) {
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
        });
    }

    public function down()
    {
        Schema::table('simpro_sites', function (Blueprint $table) {
            $table->dropColumn(['address', 'postal_code']);
        });
    }
}