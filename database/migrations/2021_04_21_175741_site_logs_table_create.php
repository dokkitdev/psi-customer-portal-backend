<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SiteLogsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('site_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('site_id')->unique();
            $table->enum('handle_status', ['new', 'error'])->default('new');
            $table->json('handle_result')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_logs');
    }
}
