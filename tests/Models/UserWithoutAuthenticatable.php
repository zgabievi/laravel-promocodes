<?php

namespace Zorb\Promocodes\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Zorb\Promocodes\Traits\AppliesPromocode;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserWithoutAuthenticatable extends Model
{
    use HasFactory, AppliesPromocode;

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
