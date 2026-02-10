<?php

namespace App\Console\Commands\Simpro;

use App\ApiClients\SimproApiClient;
use App\Console\Commands\TimeoutCommand;

class HandleUpdateSimProQuoteStatusCommand extends TimeoutCommand
{
    protected $signature = 'simpro:update-quote-status';

    protected $description = 'Handle Simpro Quote Status';

    protected SimproApiClient $simproClient;
    public function handle()
    {
        $this->simproClient = app(SimproApiClient::class);

        $quoteStates  = $this->simproClient->getListAllProjectStatusCodes(0);
        foreach ($quoteStates as $status){
            dump($status);
        }
    }
}
