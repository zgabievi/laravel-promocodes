<?php

namespace Zorb\Promocodes\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Zorb\Promocodes\Traits\AppliesPromocode;

class User extends Authenticatable
{
    use AppliesPromocode, HasFactory;

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
