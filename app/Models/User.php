<?php

namespace App\Models;

use App\Traits\HasLoggable;
use Endropie\ApiToolkit\Traits\HasFilterable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory, HasFilterable, HasLoggable;

    protected $appends = ['fullname'];

    protected $attributes = [
        'ability' => '[]'
    ];

    protected $fillable = [
        'name', 'email', 'mobile', 'ability'
    ];

    protected $casts = [
        'ability' => 'array'
    ];

    protected $hidden = [
        'password',
    ];

    public function getFullnameAttribute()
    {
        return $this->name ?? $this->email ?? $this->mobile;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims()
    {
        return [];
    }
}
