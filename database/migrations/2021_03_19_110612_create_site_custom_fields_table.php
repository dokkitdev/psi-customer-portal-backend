<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteCustomFieldsTable extends Migration
{
    public function up()
    {
        Schema::create('site_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simpro_site_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('custom_field_id');
            $table->string('value')->nullable();
            $table->timestamps();

            $table->unique([
                'simpro_site_id',
                'custom_field_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_custom_fields');
    }
}
