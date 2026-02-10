<?php

namespace App\Services;

use RonasIT\Support\Services\EntityService;

class SimproWebhookService extends EntityService
{
    protected SimproJobService $simproJobService;

    public function __construct()
    {
        $this->simproJobService = app(SimproJobService::class);
    }

    public function isWebhookVerified($header, $body)
    {
        $webhookSecret = config('services.simpro.webhook_secret');
        if (is_null($webhookSecret)) {
            return true;
        }

        return hash_equals($header, hash_hmac('sha1', $body, $webhookSecret));
    }

    public function process($data)
    {
        return $this->simproJobService->create(['data' => $data]);
    }
}
