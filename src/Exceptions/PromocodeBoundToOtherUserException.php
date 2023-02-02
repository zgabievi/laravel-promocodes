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
        parent::__construct("Ce code Promo `{$code}` est lié à un autre client, pas pour client avec le nom {$user->full_name}.");
    }
}
