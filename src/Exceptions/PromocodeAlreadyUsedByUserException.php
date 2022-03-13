<?php

namespace Zorb\Promocodes\Exceptions;

use Illuminate\Foundation\Auth\User;
use InvalidArgumentException;

class PromocodeAlreadyUsedByUserException extends InvalidArgumentException
{
    /**
     * @param User $user
     * @param string $code
     * @return void
     */
    public function __construct(User $user, string $code)
    {
        parent::__construct("The given code `{$code}` is already used by user with id {$user->id}.");
    }
}
