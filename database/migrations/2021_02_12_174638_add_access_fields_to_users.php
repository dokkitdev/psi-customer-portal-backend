<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccessFieldsToUsers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('invoices', ['View', 'No Access'])->default('No Access');
            $table->enum('quotes', ['View', 'Edit', 'No Access'])->default('No Access');
            $table->boolean('is_quote_requests')->default(false);
            $table->boolean('is_job_requests')->default(false);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'invoices',
                'quotes',
                'is_quote_requests',
                'is_job_requests'
            ]);
        });
    }
}
