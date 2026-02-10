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

        $this->fetchPage();
    }

    public function fetchPage($page = 1)
    {
        $quoteStates  = $this->simproClient->getListAllProjectStatusCodes(0, [
            'page' => $page,
            'pageSize' => 250,
        ]);

        foreach ($quoteStates as $status){
            dump($status);
        }

        if(count($quoteStates) < 250){
            return;
        }

        $this->fetchPage(++$page);
    }
}
