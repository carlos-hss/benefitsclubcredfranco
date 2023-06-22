<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type_user',
        'points',
        'status'
    ];

    public function vouchers() {
        return $this->hasMany(Voucher::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getAuthIdentifierName()
    {
        return 'id';
    }
}
