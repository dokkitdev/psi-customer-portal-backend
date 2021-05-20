<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class AssetLogHistory extends Model
{
    use ModelTrait;

    protected $fillable = [
        'last_date',
        'count'
    ];

    protected $hidden = ['pivot'];
}