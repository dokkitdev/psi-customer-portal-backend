<?php

namespace App\Models;

use App\Models\Traits\SimproPermissionsTrait;
use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use ModelTrait, SimproPermissionsTrait;

    const PERMITTED_CUSTOMERS_RELATION_PATH = 'simpro_site.simpro_customer.groups.users';
    const PERMITTED_SITES_RELATION_PATH = 'simpro_site.group_simpro_sites';

    const TYPE_PARENT = 'Parent';
    const TYPE_CHILD = 'Child';

    protected $fillable = [
        'asset_id',
        'simpro_site_id',
        'type',
        'parent_id',
        'last_test_date',
        'next_service_date',
        'name',
        'last_test_result',
        'service_level_name',
        'archived'
    ];

    protected $hidden = ['pivot'];

    public function simpro_site()
    {
        return $this->belongsTo(SimproSite::class);
    }

    public function asset_custom_fields()
    {
        return $this->hasMany(AssetCustomField::class);
    }

    public function asset_attachments()
    {
        return $this->hasMany(AssetAttachment::class);
    }

    public function asset_test_records()
    {
        return $this->hasMany(AssetTestRecord::class);
    }

    public function parent()
    {
        return $this->belongsTo(Asset::class);
    }
}