<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SiteContactsTableAddGivenAndFamilyName extends Migration
{
    public function up()
    {
        Schema::table('site_contacts', function (Blueprint $table) {
            $table->string('given_name')->nullable();
            $table->string('family_name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('site_contacts', function (Blueprint $table) {
            $table->dropColumn(['given_name', 'family_name']);
        });
    }
}
