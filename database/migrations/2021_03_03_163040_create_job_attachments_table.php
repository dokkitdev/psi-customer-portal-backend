<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobAttachmentsTable extends Migration
{
    public function up()
    {
        Schema::create('job_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('attachment_id');
            $table->string('name');
            $table->timestamps();

            $table->unique([
                'job_id',
                'attachment_id'
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_attachments');
    }
}
