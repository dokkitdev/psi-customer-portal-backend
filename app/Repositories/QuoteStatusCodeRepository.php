<?php

namespace App\Repositories;

use App\Models\QuoteStatusCode;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property QuoteStatusCode $model
*/
class QuoteStatusCodeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(QuoteStatusCode::class);
    }
}
