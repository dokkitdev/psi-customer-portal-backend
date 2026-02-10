<?php

namespace App\Console\Commands;

use App\Services\SimproCustomerService;
use Illuminate\Console\Command;

class GetSimproCustomers extends Command
{
    protected $signature = 'simpro:get-customers';

    protected $description = 'Get Simpro Customers';

    public function handle()
    {
        app(SimproCustomerService::class)->syncCustomers();

        $this->line('Simpro Customers saved');
    }
}
