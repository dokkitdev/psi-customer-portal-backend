<?php

namespace App\Services;

use App\Repositories\AssetAttachmentRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property AssetAttachmentRepository $repository
 * @mixin AssetAttachmentRepository
 */
class AssetAttachmentService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(AssetAttachmentRepository::class);
    }
}
