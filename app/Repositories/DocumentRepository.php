<?php

namespace App\Repositories;

use App\Models\Document;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property Document $model
*/
class DocumentRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Document::class);
    }
}
