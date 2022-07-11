<?php

namespace Zorb\Promocodes\Exceptions;

use InvalidArgumentException;

class UserRequiredToAcceptPromocode extends InvalidArgumentException
{
    /**
     * @param string $code
     * @return void
     */
    public function __construct(string $code)
    {
        parent::__construct("The given code `{$code}` requires to be used by user, not by guest.");
    }
}
