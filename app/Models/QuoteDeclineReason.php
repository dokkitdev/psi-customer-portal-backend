<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class QuoteDeclineReason extends Model
{
    use ModelTrait;

    protected $fillable = [
        'title',
    ];

    protected $hidden = ['pivot'];
}