<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\QuoteLogRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property QuoteLogRepository $repository
 * @mixin QuoteLogRepository
 */
class QuoteLogService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected $companyId;

    public function __construct()
    {
        $this->setRepository(QuoteLogRepository::class);

        $this->simproClient = app(SimproApiClient::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function saveAllQuotes()
    {
        $quotePages = $this->simproClient->getQuotesAsGenerator($this->companyId);

        foreach ($quotePages as $quotePage) {
            foreach ($quotePage as $quoteFromSimpro) {
                $this->createOrUpdate($quoteFromSimpro);
            }
        }
    }

    protected function createOrUpdate($quote)
    {
        return $this->repository->updateOrCreate(['quote_id' => $quote['ID']], []);
    }
}
