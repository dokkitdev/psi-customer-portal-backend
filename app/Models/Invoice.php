<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use ModelTrait;

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

    public function scopeGroupPermissions($query, $userId)
    {
        return
            $query
                ->whereHas('job.simpro_customer.groups.users', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->whereHas('job.simpro_site.group_simpro_sites', function ($query) use ($userId) {
                    $query
                        ->where('is_enabled', true)
                        ->whereHas('group.users', function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        });
                });
    }
}