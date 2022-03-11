<?php

namespace Zorb\Promocodes\Exceptions;

use InvalidArgumentException;

class PromocodeDoesNotExistException extends InvalidArgumentException
{
    /**
     * @param string $code
     * @return static
     */
    public static function create(string $code): static
    {
        return new static("The given code `{$code}` doesn't exist.");
    }
}
