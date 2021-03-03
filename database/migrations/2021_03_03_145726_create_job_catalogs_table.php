<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobCatalogsTable extends Migration
{
    public function up()
    {
        Schema::create('job_catalogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('section_id');
            $table->integer('cost_center_id');
            $table->integer('catalog_id');
            $table->integer('original_catalog_id')->nullable();
            $table->string('name')->nullable();
            $table->string('part_no')->nullable();
            $table->decimal('qty')->nullable();
            $table->timestamps();

            $table->unique([
                'job_id',
                'section_id',
                'cost_center_id',
                'catalog_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_catalogs');
    }
}
