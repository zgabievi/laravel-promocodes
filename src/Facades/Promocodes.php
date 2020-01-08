<?php

namespace Gabievi\Promocodes\Facades;

use Gabievi\Promocodes\Models\Promocode;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Class Promocodes
 * @package Gabievi\Promocodes\Facades
 * @method static array output(int $amount = 1)
 * @method static Collection create(int $amount = 1, float $reward = null, array $data = [], int $expires_in = null, int $quantity = null, bool $is_disposable = false)
 * @method static Collection createDisposable(int $amount = 1, float $reward = null, array $data = [], int $expires_in = null, int $quantity = null)
 * @method static bool|Promocode check(string $code)
 * @method static bool|Promocode apply(string $code)
 * @method static bool|Promocode redeem(string $code)
 * @method static bool disable(string $code)
 * @method static void clearRedundant()
 * @method static Promocode[]|\Illuminate\Database\Eloquent\Collection all()
 * @method static bool isSecondUsageAttempt(Promocode $promocode)
 */
class Promocodes extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'promocodes';
    }
}
