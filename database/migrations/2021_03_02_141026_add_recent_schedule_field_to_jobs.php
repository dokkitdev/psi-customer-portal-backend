<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecentScheduleFieldToJobs extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->foreignId('recent_schedule_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('recent_schedule_id');
        });
    }
}
