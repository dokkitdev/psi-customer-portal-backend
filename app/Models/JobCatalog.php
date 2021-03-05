<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class JobCatalog extends Model
{
    use ModelTrait;

    protected $fillable = [
        'job_id',
        'section_id',
        'cost_center_id',
        'catalog_id',
        'original_catalog_id',
        'name',
        'part_no',
        'qty'
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'qty' => 'float'
    ];
}