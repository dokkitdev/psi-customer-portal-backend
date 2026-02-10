<?php

namespace App\Repositories;

use App\Models\QuoteDeclineReason;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property QuoteDeclineReason $model
*/
class QuoteDeclineReasonRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(QuoteDeclineReason::class);
    }
}
