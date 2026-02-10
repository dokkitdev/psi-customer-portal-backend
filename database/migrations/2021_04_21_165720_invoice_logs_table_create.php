<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InvoiceLogsTableCreate extends Migration
{
    public function up()
    {
        Schema::create('invoice_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->unique();
            $table->enum('handle_status', ['new', 'error'])->default('new');
            $table->json('handle_result')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_logs');
    }
}
