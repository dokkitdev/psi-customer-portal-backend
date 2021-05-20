<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class AssetLogDate extends Model
{
    use ModelTrait;

    protected $fillable = [
        'last_date',
    ];

    protected $hidden = ['pivot'];
}