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

    public function getForHandle($limit = 100)
    {
        return $this
            ->getQuery(['handle_status' => QuoteLog::HANDLE_STATUS_NEW])
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();
    }
}
