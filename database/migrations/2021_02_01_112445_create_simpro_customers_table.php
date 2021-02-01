<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimproCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('simpro_customers', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->string('name');
            $table->enum('type', ['individuals', 'companies']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('simpro_customers');
    }
}
