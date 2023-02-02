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
        parent::__construct("Ce code Promo `{$code}` doit être utilisé par un client authentifié, et non par un invité.");
    }
}
