<?php

namespace App\Services;

use App\Models\Quote;
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

    public function update($where, $data)
    {
        $data['stage'] = $this->getMappedQuoteStage($data['status']);

        return $this->repository->update($where, $data);
    }

    protected function getMappedQuoteStage($status)
    {
        if ($status === Quote::STATUS_NEW) {
            return Quote::STAGE_IN_PROGRESS;
        }

        if (in_array($status, [Quote::STATUS_PENDING, Quote::STATUS_ACCEPTED, Quote::STATUS_DECLINED])) {
            return Quote::STAGE_SENT;
        }

        return null;
    }
}
