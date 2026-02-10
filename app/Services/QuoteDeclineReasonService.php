<?php

namespace App\Services;

use App\Repositories\QuoteDeclineReasonRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property QuoteDeclineReasonRepository $repository
 * @mixin QuoteDeclineReasonRepository
 */
class QuoteDeclineReasonService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(QuoteDeclineReasonRepository::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterByQuery(['title'])
            ->getSearchResults();
    }
}
