<?php

namespace Zorb\Promocodes\Exceptions;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class PromocodeAlreadyUsedByUserException extends InvalidArgumentException
{
    /**
     * @param Model $user
     * @param string $code
     * @return void
     */
    public function __construct(Model $user, string $code)
    {
        parent::__construct("Ce code Promo `{$code}` est déjà utilisé par un autre client.");
    }
}
