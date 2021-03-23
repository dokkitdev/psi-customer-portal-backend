<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class SiteContact extends Model
{
    use ModelTrait;

    protected $fillable = [
        'simpro_site_id',
        'contact_id',
        'title',
        'name',
        'email',
        'work_phone',
        'cell_phone',
        'position',
        'is_primary'
    ];

    protected $hidden = ['pivot'];
}