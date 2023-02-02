<?php

namespace Zorb\Promocodes\Exceptions;

use InvalidArgumentException;

class PromocodeNoUsagesLeftException extends InvalidArgumentException
{
    /**
     * @param string $code
     * @return void
     */
    public function __construct(string $code)
    {
        parent::__construct("Ce code Promo `{$code}` n'a plus d'usages.");
    }
}
