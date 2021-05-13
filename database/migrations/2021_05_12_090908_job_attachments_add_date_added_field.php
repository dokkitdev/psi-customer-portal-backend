<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobAttachmentsAddDateAddedField extends Migration
{
    public function up()
    {
        Schema::table('job_attachments', function (Blueprint $table) {
            $table->dateTime('date_added')->nullable();
        });
    }

    public function down()
    {
        Schema::table('job_attachments', function (Blueprint $table) {
            $table->dropColumn('date_added');
        });
    }
}
