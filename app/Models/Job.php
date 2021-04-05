<?php

namespace App\Models;

use App\Models\Traits\SimproPermissionsTrait;
use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use ModelTrait, SimproPermissionsTrait;

    const OPEN_STAGES = [
        'Pending',
        'Progress'
    ];

    const BUSINESS_GROUPS = [
        'Maintenance',
        'Reactives',
        'Small Works',
        'Projects',
        'Supply Only'
    ];

    const PERMITTED_CUSTOMERS_RELATION_PATH = 'simpro_customer.groups.users';
    const PERMITTED_SITES_RELATION_PATH = 'simpro_site.group_simpro_sites';

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
        'requested',
        'recent_schedule_id',
        'name'
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

    public function recent_schedule()
    {
        return $this->belongsTo(Schedule::class, 'recent_schedule_id', 'id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function job_catalogs()
    {
        return $this->hasMany(JobCatalog::class);
    }

    public function job_attachments()
    {
        return $this->hasMany(JobAttachment::class);
    }

    public function job_work_orders()
    {
        return $this->hasMany(JobWorkOrder::class);
    }
}