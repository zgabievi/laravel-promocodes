<?php

namespace Gabievi\Promocodes\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Gabievi\Promocodes\Traits\Rewardable;
use Illuminate\Contracts\Auth\Authenticatable;

class User extends Model implements Authenticatable
{
    use Rewardable;

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
