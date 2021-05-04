<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetsTableDropColumnSimproCustomerId extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('simpro_customer_id');
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('simpro_customer_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
}
