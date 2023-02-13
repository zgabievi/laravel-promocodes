<?php

namespace Zorb\Promocodes\Exceptions;

use InvalidArgumentException;

class CurrencyRequiredToAcceptPromocode extends InvalidArgumentException
{
    /**
     * @param string $code
     * @return void
     */
    public function __construct(string $code)
    {
        parent::__construct("Ce code Promo `{$code}` doit être fournis avec devise spécifiée");
    }
}
