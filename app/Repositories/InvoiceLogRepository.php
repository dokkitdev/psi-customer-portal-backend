<?php

namespace App\Repositories;

use App\Models\InvoiceLog;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property InvoiceLog $model
*/
class InvoiceLogRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(InvoiceLog::class);
    }

    public function getForHandle($limit = 100)
    {
        return $this
            ->getQuery(['handle_status' => InvoiceLog::HANDLE_STATUS_NEW])
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();
    }
}
