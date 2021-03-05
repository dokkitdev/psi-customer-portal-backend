<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class JobAttachment extends Model
{
    use ModelTrait;

    protected $fillable = [
        'job_id',
        'attachment_id',
        'name',
    ];

    protected $hidden = ['pivot'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}