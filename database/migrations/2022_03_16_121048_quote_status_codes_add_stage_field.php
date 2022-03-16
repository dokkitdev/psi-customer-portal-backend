<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuoteStatusCodesAddStageField extends Migration
{
    public function up()
    {
        Schema::table('quote_status_codes', function (Blueprint $table) {
            $table->string('stage')->nullable();
        });
    }

    public function down()
    {
        Schema::table('quote_status_codes', function (Blueprint $table) {
            $table->dropColumn('stage');
        });
    }
}
