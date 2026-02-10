<?php

namespace App\Repositories;

use App\Models\SiteCustomField;

/**
 * @property SiteCustomField $model
*/
class SiteCustomFieldRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(SiteCustomField::class);
    }
}
