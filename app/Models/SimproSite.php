<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class SimproSite extends Model
{
    use ModelTrait;

    protected $fillable = [
        'site_id',
        'name',
        'address',
        'postal_code'
    ];

    protected $hidden = ['pivot'];

    public function group_simpro_sites()
    {
        return $this->hasMany(GroupSimproSite::class);
    }
}