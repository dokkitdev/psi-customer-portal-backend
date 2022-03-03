<?php

namespace App\Models;

use App\Models\Traits\SimproPermissionsTrait;
use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use ModelTrait, SimproPermissionsTrait;

    const TYPE_PPM_QUOTE = 1;
    const TYPE_REMEDIAL_INSTALLATION_QUOTE = 2;

    const TYPES = [
        self::TYPE_PPM_QUOTE,
        self::TYPE_REMEDIAL_INSTALLATION_QUOTE
    ];

    const STAGE_IN_PROGRESS = 'InProgress';
    const STAGE_SENT = 'Sent';

    const STATUS_NEW = 'New';
    const STATUS_PENDING = 'Pending';
    const STATUS_DECLINED = 'Declined';
    const STATUS_ACCEPTED = 'Accepted';

    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_PENDING,
        self::STATUS_DECLINED,
        self::STATUS_ACCEPTED
    ];

    const PERMITTED_CUSTOMERS_RELATION_PATH = 'simpro_customer.groups.users';
    const PERMITTED_SITES_RELATION_PATH = 'simpro_site.group_simpro_sites';

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
        'attachment_name',
        'name',
        'business_group',
        'status_id'
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
}