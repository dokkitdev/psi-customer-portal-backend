<?php

namespace App\Console\Commands\Simpro;

use App\ApiClients\SimproApiClient;
use App\Services\QuoteService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class UpdateQuotes extends Command
{
    protected $signature = 'simpro:update-quotes';

    protected $description = 'Update Simpro quotes';

    protected QuoteService $quoteService;
    protected SimproApiClient $simproClient;

    public function handle()
    {
        $this->simproClient = app(SimproApiClient::class);
        $this->quoteService = app(QuoteService::class);

        $this->quoteService->chunk(1000, function ($quotes) {
            foreach ($quotes as $quote) {
                try {
                    list($note, $attachment) = $this->quoteService->getNoteAndAttachment(0, $quote['quote_id'], $quote['note_id']);

                    $this->quoteService->update($quote['id'], [
                        'note_id' => Arr::get($note, 'ID'),
                        'note' => Arr::get($note, 'Note'),
                        'attachment_id' => Arr::get($attachment, 'ID'),
                        'attachment_name' => Arr::get($attachment, 'Filename'),
                    ]);
                } catch (Exception $e) {
                    report($e);
                }
            }
        });

        $this->line('Simpro Quotes updated');
    }
}
