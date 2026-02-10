<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobWorkOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('job_work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('section_id');
            $table->integer('cost_center_id');
            $table->integer('work_order_id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();

            $table->unique([
                'job_id',
                'section_id',
                'cost_center_id',
                'work_order_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_work_orders');
    }
}
