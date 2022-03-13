<?php

namespace Zorb\Promocodes\Exceptions;

use InvalidArgumentException;

class PromocodeExpiredException extends InvalidArgumentException
{
    /**
     * @param string $code
     * @return void
     */
    public function __construct(string $code)
    {
        parent::__construct("The given code `{$code}` already expired.");
    }
}
