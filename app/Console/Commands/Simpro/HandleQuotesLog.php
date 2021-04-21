<?php

namespace App\Console\Commands\Simpro;

use App\Models\QuoteLog;
use App\Services\QuoteLogService;
use App\Services\QuoteService;
use Illuminate\Console\Command;
use Exception;

class HandleQuotesLog extends Command
{
    protected $signature = 'quotes-log:handle';

    protected $description = 'Handle Quotes Log';

    protected QuoteLogService $quoteLogService;
    protected QuoteService $quoteService;

    public function handle()
    {
        $this->quoteLogService = app(QuoteLogService::class);
        $this->quoteService = app(QuoteService::class);

        $this->quoteLogService
            ->getForHandle(1000)
            ->each(function ($quoteLog) {
                try {
                    $this->quoteService->updateOrCreateBySimpro([
                        'data' => [
                            'reference' => [
                                'companyID' => 0,
                            ],
                            'description' => "{$quoteLog['quote_id']}"
                        ]
                    ]);

                    $this->quoteLogService->delete($quoteLog['id']);
                } catch (Exception $e) {
                    report($e);

                    $this->quoteLogService->update($quoteLog['id'], [
                        'handle_status' => QuoteLog::HANDLE_STATUS_ERROR,
                        'handle_result' => [
                            'code' => $e->getCode(),
                            'message' => $e->getMessage()
                        ]
                    ]);
                }
            });
    }
}
