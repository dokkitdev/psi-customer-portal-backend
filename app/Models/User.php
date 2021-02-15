<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use RonasIT\Support\Traits\ModelTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, ModelTrait;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'set_password_hash_created_at',
        'invoice_permission_level',
        'quote_permission_level',
        'is_quote_requests',
        'is_job_requests'
    ];

    protected $guarded = [
        'set_password_hash'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'set_password_hash'
    ];

    protected $dates = [
        'set_password_hash_created_at'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }
}
