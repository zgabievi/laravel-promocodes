<?php

namespace Zorb\Promocodes\Exceptions;

use Illuminate\Foundation\Auth\User;
use InvalidArgumentException;

class PromocodeBoundToOtherUserException extends InvalidArgumentException
{
    /**
     * @param User $user
     * @param string $code
     * @return void
     */
    public function __construct(User $user, string $code)
    {
        parent::__construct("The given code `{$code}` is bound to other user, not user with id {$user->id}.");
    }
}
