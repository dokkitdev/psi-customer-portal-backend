<?php

namespace App\Models;

use App\Models\Traits\SimproPermissionsTrait;
use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use ModelTrait, SimproPermissionsTrait;

    const PERMITTED_CUSTOMERS_RELATION_PATH = 'job.simpro_customer.groups.users';
    const PERMITTED_SITES_RELATION_PATH = 'job.simpro_site.group_simpro_sites';

    protected $fillable = [
        'job_id',
        'invoice_id',
        'date_issued',
        'status',
        'total',
        'date_paid'
    ];

    protected $casts = [
        'total' => 'float'
    ];

    protected $hidden = ['pivot'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}