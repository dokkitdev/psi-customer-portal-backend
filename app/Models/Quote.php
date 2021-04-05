<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use ModelTrait;

    const TYPE_PPM_QUOTE = 1;
    const TYPE_REMEDIAL_INSTALLATION_QUOTE = 2;

    const TYPES = [
        self::TYPE_PPM_QUOTE,
        self::TYPE_REMEDIAL_INSTALLATION_QUOTE
    ];

    const STAGE_APPROVED = 'Approved';

    const STATUS_NEW = 'New';
    const STATUS_PENDING = 'Pending';
    const STATUS_DECLINED = 'Declined';

    protected $fillable = [
        'job_id',
        'simpro_customer_id',
        'simpro_site_id',
        'quote_id',
        'date_issued',
        'status',
        'stage',
        'description',
        'cost_center_name',
        'value',
        'date_expiry',
        'note_id',
        'note',
        'attachment_id',
        'attachment_name'
    ];

    protected $casts = [
        'value' => 'float'
    ];

    protected $hidden = ['pivot'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function simpro_customer()
    {
        return $this->belongsTo(SimproCustomer::class);
    }

    public function simpro_site()
    {
        return $this->belongsTo(SimproSite::class);
    }

    public function scopeGroupPermissions($query, $userId)
    {
        return
            $query
                ->whereHas('simpro_customer.groups.users', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->whereHas('simpro_site.group_simpro_sites', function ($query) use ($userId) {
                    $query
                        ->where('is_enabled', true)
                        ->whereHas('group.users', function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        });
                });
    }
}