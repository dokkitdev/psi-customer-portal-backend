<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetLogDatesCreate extends Migration
{
    public function up()
    {
        Schema::create('asset_log_dates', function (Blueprint $table) {
            $table->id();
            $table->dateTime('last_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_log_dates');
    }
}
