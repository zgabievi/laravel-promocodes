<?php

namespace Zorb\Promocodes\Facades;

use Illuminate\Support\Facades\Facade;

class Promocodes extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'promocodes';
    }
}
