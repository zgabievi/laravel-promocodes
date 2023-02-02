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
        $message = $code ? "Ce code Promo `{$code}` n'existe pas." : "Le code promo n'a pas été fourni.";

        parent::__construct($message);
    }
}
