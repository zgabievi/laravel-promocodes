<?php

namespace Zorb\Promocodes\Exceptions;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class PromocodeBoundToOtherCurrencyException extends InvalidArgumentException
{
    /**
     * @param Model $currency
     * @param string $code
     * @return void
     */
    public function __construct(Model $currency, string $code)
    {
        parent::__construct("Ce code Promo `{$code}` est lié à un autre devise, pas pour {$currency->name}.");
    }
}
