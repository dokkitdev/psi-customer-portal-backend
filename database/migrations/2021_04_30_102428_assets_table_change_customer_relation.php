<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetsTableChangeCustomerRelation extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->bigInteger('simpro_customer_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->bigInteger('simpro_customer_id')->change();
        });
    }
}
