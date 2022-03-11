<?php

namespace Zorb\Promocodes\Exceptions;

use InvalidArgumentException;

class PromocodeExpiredException extends InvalidArgumentException
{
    /**
     * @param string $code
     * @return static
     */
    public static function create(string $code): static
    {
        return new static("The given code `{$code}` already expired.");
    }
}
