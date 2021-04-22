<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class InvoiceLog extends Model
{
    use ModelTrait;

    const HANDLE_STATUS_NEW = 'new';
    const HANDLE_STATUS_ERROR = 'error';

    protected $fillable = [
        'invoice_id',
        'handle_status',
        'handle_result'
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'handle_result' => 'array',
    ];
}