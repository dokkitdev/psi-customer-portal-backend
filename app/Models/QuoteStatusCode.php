<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class QuoteStatusCode extends Model
{
    use ModelTrait;

    protected $fillable = [
        'simpro_code_id',
        'name',
        'status',
        'stage'
    ];

    protected $hidden = ['pivot'];
}