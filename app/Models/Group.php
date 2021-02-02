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
    ];

    protected $hidden = ['pivot'];

    public function simpro_customer()
    {
        return $this->belongsTo(SimproCustomer::class);
    }
}