<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailFieldsToSimproSites extends Migration
{
    public function up()
    {
        Schema::table('simpro_sites', function (Blueprint $table) {
            $table->foreignId('simpro_customer_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
        });
    }

    public function down()
    {
        Schema::table('simpro_sites', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'country'
            ]);

            $table->dropForeign(['simpro_customer_id']);
        });
    }
}
