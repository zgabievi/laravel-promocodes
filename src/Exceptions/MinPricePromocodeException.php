<?php

namespace Zorb\Promocodes\Exceptions;

use InvalidArgumentException;

class MinPricePromocodeException extends InvalidArgumentException
{
    /**
     * @param string $code
     * @return void
     */
    public function __construct(string $code, float $min_price)
    {
        parent::__construct("Ce code Promo `{$code}` doit être utilisé pour un minimum prix de `{$min_price}`");
    }
}
