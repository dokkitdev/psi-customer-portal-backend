<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuoteLogsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('quote_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('quote_id')->unique();
            $table->enum('handle_status', ['new', 'error'])->default('new');
            $table->json('handle_result')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quote_logs');
    }
}
