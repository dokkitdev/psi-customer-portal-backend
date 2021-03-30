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
        'postal_code',
        'simpro_customer_id',
        'city',
        'country',
        'county'
    ];

    protected $hidden = ['pivot'];

    public function group_simpro_sites()
    {
        return $this->hasMany(GroupSimproSite::class);
    }

    public function simpro_customer()
    {
        return $this->belongsTo(SimproCustomer::class);
    }

    public function open_jobs()
    {
        return $this->hasMany(Job::class)->whereIn('stage', Job::OPEN_STAGES);
    }

    public function site_custom_fields()
    {
        return $this->hasMany(SiteCustomField::class);
    }

    public function reference_site_custom_field()
    {
        return $this->hasOne(SiteCustomField::class)->where('custom_field_id', SiteCustomField::REFERENCE_ID);
    }

    public function customer_ref_site_custom_field()
    {
        return $this->hasOne(SiteCustomField::class)->where('custom_field_id', SiteCustomField::CUSTOMER_REF_ID);
    }

    public function site_contacts()
    {
        return $this->hasMany(SiteContact::class);
    }

    public function primary_site_contact()
    {
        return $this->hasOne(SiteContact::class)->where('is_primary', true);
    }

    public function scopeGroupPermissions($query, $userId)
    {
        return
            $query
                ->whereHas('simpro_customer.groups.users', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->whereHas('group_simpro_sites', function ($query) use ($userId) {
                    $query
                        ->where('is_enabled', true)
                        ->whereHas('group.users', function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        });
                });
    }
}