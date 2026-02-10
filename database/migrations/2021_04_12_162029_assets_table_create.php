<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssetsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->integer('asset_id')->unique();
            $table->foreignId('simpro_customer_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('simpro_site_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->enum('type', ['Parent', 'Child']);
            $table->integer('parent_id')->nullable();
            $table->date('last_test_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->string('name')->nullable();
            $table->string('last_test_result')->nullable();
            $table->string('service_level_name')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
}
