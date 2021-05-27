<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuotesAddNameField extends Migration
{
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->text('name')->nullable();
            $table->string('business_group')->nullable();
        });
    }

    public function down()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'business_group'
            ]);
        });
    }
}
