<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class SiteCustomField extends Model
{
    use ModelTrait;

    protected $fillable = [
        'simpro_site_id',
        'custom_field_id',
        'value'
    ];

    protected $hidden = ['pivot'];
}