<?php

namespace Zorb\Promocodes\Exceptions;

use InvalidArgumentException;

class PromocodeDoesNotExistException extends InvalidArgumentException
{
    /**
     * @param string|null $code
     * @return void
     */
    public function __construct(?string $code)
    {
        $message = $code ? "The given code `{$code}` doesn't exist." : "The code was not event provided.";

        parent::__construct($message);
    }
}
