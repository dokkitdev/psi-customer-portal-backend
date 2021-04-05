<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuotesTableCreate extends Migration
{
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('simpro_customer_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('simpro_site_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('quote_id');
            $table->date('date_issued')->nullable();
            $table->string('status')->nullable();
            $table->string('stage')->nullable();
            $table->text('description')->nullable();
            $table->string('cost_center_name')->nullable();
            $table->decimal('value')->nullable();
            $table->date('date_expiry')->nullable();
            $table->integer('note_id')->nullable();
            $table->text('note')->nullable();
            $table->string('attachment_id')->nullable();
            $table->string('attachment_name')->nullable();
            $table->timestamps();

            $table->unique([
                'simpro_site_id',
                'quote_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotes');
    }
}
