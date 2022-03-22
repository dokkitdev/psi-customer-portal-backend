<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobsTableAddConvertedFromQuoteIdField extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table
                ->foreignId('converted_from_quote_id')
                ->nullable()
                ->references('id')
                ->on('quotes')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('converted_from_quote_id');
        });
    }
}
