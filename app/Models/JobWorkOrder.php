<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class JobWorkOrder extends Model
{
    use ModelTrait;

    protected $fillable = [
        'job_id',
        'section_id',
        'cost_center_id',
        'work_order_id',
        'name',
        'description',
        'date'
    ];

    protected $hidden = ['pivot'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}