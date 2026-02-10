<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuotesTableChangeForeign extends Migration
{
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropUnique([
                'simpro_site_id',
                'quote_id'
            ]);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->unique('quote_id');
        });
    }

    public function down()
    {
    }
}
