<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimproSitesTable extends Migration
{
    public function up()
    {
        Schema::create('simpro_sites', function (Blueprint $table) {
            $table->id();
            $table->integer('site_id')->unique();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('simpro_sites');
    }
}
