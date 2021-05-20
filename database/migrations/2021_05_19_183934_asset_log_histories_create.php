<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetLogHistoriesCreate extends Migration
{
    public function up()
    {
        Schema::create('asset_log_histories', function (Blueprint $table) {
            $table->id();
            $table->dateTime('last_date');
            $table->integer('count');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_log_histories');
    }
}
