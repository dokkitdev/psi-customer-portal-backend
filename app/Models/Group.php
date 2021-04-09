<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use ModelTrait;

    protected $fillable = [
        'simpro_customer_id',
        'title',
        'is_enabled_all_sites'
    ];

    protected $hidden = ['pivot'];

    public function simpro_customer()
    {
        return $this->belongsTo(SimproCustomer::class);
    }

    public function group_simpro_sites()
    {
        return $this->hasMany(GroupSimproSite::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}