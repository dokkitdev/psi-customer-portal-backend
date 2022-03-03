<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuoteStatusCodesCreate extends Migration
{
    public function up()
    {
        Schema::create('quote_status_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('simpro_code_id');
            $table->string('name');
            $table->enum('status', ['New', 'Pending', 'Declined', 'Accepted'])->nullable();
            $table->timestamps();
        });

        $this->addCodes();
    }

    public function down()
    {
        Schema::dropIfExists('quote_status_codes');
    }

    protected function addCodes()
    {
        $roles = [
            [
                'simpro_code_id' => 33,
                'name' => 'Quote : Approved to be Sent',
            ],
            [
                'simpro_code_id' => 32,
                'name' => 'Quote : Awaiting Approval',
            ],
            [
                'simpro_code_id' => 87,
                'name' => 'Quote : Awaiting Information',
            ],
            [
                'simpro_code_id' => 86,
                'name' => 'Quote : Awaiting Price',
            ],
            [
                'simpro_code_id' => 85,
                'name' => 'Quote : Awaiting Procurement',
            ],
            [
                'simpro_code_id' => 92,
                'name' => 'Quote : Declined',
            ],
            [
                'simpro_code_id' => 169,
                'name' => 'Quote : Enquiry to be Assigned',
            ],
            [
                'simpro_code_id' => 170,
                'name' => 'Quote : On Hold',
            ],
            [
                'simpro_code_id' => 31,
                'name' => 'Quote : Overdue',
            ],
            [
                'simpro_code_id' => 101,
                'name' => 'Quote : Re-requested',
            ],
            [
                'simpro_code_id' => 90,
                'name' => 'Quote : Rejected',
            ],
            [
                'simpro_code_id' => 5,
                'name' => 'Quote : Sent to Client',
            ],
            [
                'simpro_code_id' => 4,
                'name' => 'Quote : Survey Scheduled',
            ],
            [
                'simpro_code_id' => 88,
                'name' => 'Quote : Survey to be Booked',
            ],
            [
                'simpro_code_id' => 3,
                'name' => 'Quote : To Be Completed',
            ],
            [
                'simpro_code_id' => 35,
                'name' => 'Quote : Won',
            ],
            [
                'simpro_code_id' => 34,
                'name' => 'Quote : Won - Accepted Online',
            ],
            [
                'simpro_code_id' => 135,
                'name' => 'Quote: Requested via Portal',
            ],
        ];

        DB::table('quote_status_codes')->insert($roles);
    }
}
