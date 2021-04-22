<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\QuoteLog;
use App\Repositories\QuoteLogRepository;
use Exception;
use RonasIT\Support\Services\EntityService;

/**
 * @property QuoteLogRepository $repository
 * @mixin QuoteLogRepository
 */
class QuoteLogService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected QuoteService $quoteService;
    protected $companyId;

    public function __construct()
    {
        $this->setRepository(QuoteLogRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->quoteService = app(QuoteService::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterBy('handle_status')
            ->getSearchResults();
    }

    public function saveAllQuotes()
    {
        $quotePages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/quotes/");

        foreach ($quotePages as $quotePage) {
            foreach ($quotePage as $quoteFromSimpro) {
                $this->repository->updateOrCreate(['quote_id' => $quoteFromSimpro['ID']], []);
            }
        }
    }

    public function handleLog()
    {
        $quoteLogs = $this->search([
            'handle_status' => QuoteLog::HANDLE_STATUS_NEW,
            'page' => 1,
            'per_page' => 1000,
            'order_by' => 'id',
        ]);

        foreach ($quoteLogs['data'] as $quoteLog) {
            try {
                $this->quoteService->updateOrCreateBySimpro([
                    'data' => [
                        'reference' => [
                            'companyID' => 0,
                        ],
                        'description' => "{$quoteLog['quote_id']}"
                    ]
                ]);

                $this->delete($quoteLog['id']);
            } catch (Exception $e) {
                report($e);

                $this->update($quoteLog['id'], [
                    'handle_status' => QuoteLog::HANDLE_STATUS_ERROR,
                    'handle_result' => [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage()
                    ]
                ]);
            }
        }
    }
}
