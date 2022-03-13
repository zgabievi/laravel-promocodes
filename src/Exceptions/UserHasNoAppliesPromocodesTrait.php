<?php

namespace Zorb\Promocodes\Exceptions;

use Illuminate\Foundation\Auth\User;
use InvalidArgumentException;

class UserHasNoAppliesPromocodesTrait extends InvalidArgumentException
{
    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct("The given user model doesn't have AppliesPromocodes trait.");
    }
}
