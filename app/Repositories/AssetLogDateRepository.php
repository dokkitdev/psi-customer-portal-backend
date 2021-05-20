<?php

namespace App\Repositories;

use App\Models\AssetLogDate;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property AssetLogDate $model
*/
class AssetLogDateRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(AssetLogDate::class);
    }

    public function last()
    {
        return $this->getQuery()
            ->orderBy('id', 'desc')
            ->first();
    }
}
