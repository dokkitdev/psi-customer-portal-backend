<?php

namespace App\Repositories;

use App\Models\AssetLog;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property AssetLog $model
*/
class AssetLogRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(AssetLog::class);
    }
}
