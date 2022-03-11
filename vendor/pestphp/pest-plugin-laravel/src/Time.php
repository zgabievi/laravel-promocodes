<?php

declare(strict_types=1);

namespace Pest\Laravel;

use DateTimeInterface;

/**
 * Begin travelling to another time.
 *
 * @param int $value
 *
 * @return \Illuminate\Foundation\Testing\Wormhole
 */
function travel($value)
{
    return test()->travel(...func_get_args());
}

/**
 * Travel to another time.
 *
 * @param callable|null $callback
 *
 * @return mixed
 */
function travelTo(DateTimeInterface $date, $callback = null)
{
    return test()->travelTo(...func_get_args());
}

/**
 * Travel back to the current time.
 *
 * @return \DateTimeInterface
 */
function travelBack()
{
    return test()->travelBack(...func_get_args());
}
