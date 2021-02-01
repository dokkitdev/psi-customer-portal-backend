<?php

namespace App\Services;

use App\Repositories\QuoteRerequestReasonRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property QuoteRerequestReasonRepository $repository
 * @mixin QuoteRerequestReasonRepository
 */
class QuoteRerequestReasonService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(QuoteRerequestReasonRepository::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterByQuery(['title'])
            ->getSearchResults();
    }
}
