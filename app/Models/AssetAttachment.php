<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class AssetAttachment extends Model
{
    use ModelTrait;

    protected $fillable = [
        'asset_id',
        'attachment_id',
        'name',
    ];

    protected $hidden = ['pivot'];
}