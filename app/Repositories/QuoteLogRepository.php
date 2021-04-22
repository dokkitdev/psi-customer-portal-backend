<?php

namespace App\Repositories;

use App\Models\QuoteLog;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property QuoteLog $model
*/
class QuoteLogRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(QuoteLog::class);
    }
}
