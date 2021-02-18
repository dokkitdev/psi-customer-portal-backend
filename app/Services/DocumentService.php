<?php

namespace App\Services;

use App\Repositories\DocumentRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property DocumentRepository $repository
 * @mixin DocumentRepository
 */
class DocumentService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(DocumentRepository::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->with()
            ->getSearchResults();
    }
}
