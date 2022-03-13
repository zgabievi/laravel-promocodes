<?php

namespace Zorb\Promocodes\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserWithoutTrait extends Authenticatable
{
    use HasFactory;

    //
    protected $table = 'users';

    //
    protected $guarded = [];

    //
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    //
    public function getAuthIdentifier()
    {
        return $this->attributes['id'];
    }

    //
    public function getAuthPassword()
    {
        return $this->attributes['password'];
    }

    //
    public function getRememberToken()
    {
        return 'token';
    }

    //
    public function setRememberToken($value)
    {
        //
    }

    //
    public function getRememberTokenName()
    {
        return 'tokenName';
    }
}
