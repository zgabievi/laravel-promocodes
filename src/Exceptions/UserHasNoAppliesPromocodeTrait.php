<?php

namespace Zorb\Promocodes\Exceptions;

use Illuminate\Foundation\Auth\User;
use InvalidArgumentException;

class UserHasNoAppliesPromocodeTrait extends InvalidArgumentException
{
    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct("The given user model doesn't have AppliesPromocode trait.");
    }
}
