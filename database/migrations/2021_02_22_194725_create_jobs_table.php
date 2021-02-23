<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id')->unique();
            $table->foreignId('simpro_customer_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('simpro_site_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('priority')->nullable();
            $table->string('cost_center_name')->nullable();
            $table->string('business_group')->nullable();
            $table->date('date_created')->nullable();
            $table->string('stage')->nullable();
            $table->string('job_status')->nullable();
            $table->date('requested')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
