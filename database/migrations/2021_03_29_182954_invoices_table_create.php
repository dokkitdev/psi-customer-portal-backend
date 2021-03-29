<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InvoicesTableCreate extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('invoice_id');
            $table->date('date_issued')->nullable();
            $table->string('status')->nullable();
            $table->decimal('total')->nullable();
            $table->date('date_paid')->nullable();
            $table->timestamps();

            $table->unique([
                'job_id',
                'invoice_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
