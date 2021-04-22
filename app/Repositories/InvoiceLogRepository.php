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
}
