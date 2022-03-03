<?php

namespace App\Services;

use App\Repositories\QuoteStatusCodeRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property QuoteStatusCodeRepository $repository
 * @mixin QuoteStatusCodeRepository
 */
class QuoteStatusCodeService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(QuoteStatusCodeRepository::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterBy('status')
            ->filterByQuery(['name'])
            ->getSearchResults();
    }
}
