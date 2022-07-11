<?php

namespace Zorb\Promocodes\Exceptions;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class PromocodeBoundToOtherUserException extends InvalidArgumentException
{
    /**
     * @param Model $user
     * @param string $code
     * @return void
     */
    public function __construct(Model $user, string $code)
    {
        parent::__construct("The given code `{$code}` is bound to other user, not user with id {$user->id}.");
    }
}
