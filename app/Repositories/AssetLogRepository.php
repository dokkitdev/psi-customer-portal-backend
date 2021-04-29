<?php

namespace App\Repositories;

use App\Models\AssetLog;
use App\Models\JobLog;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property JobLog $model
*/
class AssetLogRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(AssetLog::class);
    }
}
