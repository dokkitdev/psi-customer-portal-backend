<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteDeclineReasonsTable extends Migration
{
    public function up()
    {
        Schema::create('quote_decline_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quote_decline_reasons');
    }
}
