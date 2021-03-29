<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobCatalogsTableChangeNameField extends Migration
{
    public function up()
    {
        Schema::table('job_catalogs', function (Blueprint $table) {
            $table->text('name')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('job_catalogs', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
        });
    }
}
