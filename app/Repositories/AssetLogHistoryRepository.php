<?php

namespace App\Repositories;

use App\Models\AssetLogHistory;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property AssetLogHistory $model
*/
class AssetLogHistoryRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(AssetLogHistory::class);
    }

    public function last()
    {
        return $this->getQuery()
            ->orderBy('id', 'desc')
            ->first();
    }
}
