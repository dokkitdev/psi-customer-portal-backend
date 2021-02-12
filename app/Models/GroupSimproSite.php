<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class GroupSimproSite extends Model
{
    use ModelTrait;

    protected $table = 'group_simpro_site';

    protected $fillable = [
        'group_id',
        'simpro_site_id',
        'is_enabled'
    ];

    protected $hidden = ['pivot'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function simpro_site()
    {
        return $this->belongsTo(SimproSite::class);
    }
}