<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use ModelTrait;

    protected $fillable = [
        'job_id',
        'simpro_customer_id',
        'simpro_site_id',
        'description',
        'priority',
        'cost_center_name',
        'business_group',
        'date_created',
        'stage',
        'job_status',
        'requested'
    ];

    protected $hidden = ['pivot'];

    public function simpro_customer()
    {
        return $this->belongsTo(SimproCustomer::class);
    }

    public function simpro_site()
    {
        return $this->belongsTo(SimproSite::class);
    }
}