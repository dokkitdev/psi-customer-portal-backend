<?php

namespace App\Repositories;

use App\Models\QuoteRerequestReason;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property QuoteRerequestReason $model
*/
class QuoteRerequestReasonRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(QuoteRerequestReason::class);
    }
}
