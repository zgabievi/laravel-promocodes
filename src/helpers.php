<?php

use Zorb\Promocodes\Exceptions\PromocodeDoesNotExistException;
use Zorb\Promocodes\Contracts\PromocodeContract;
use Zorb\Promocodes\Facades\Promocodes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Carbon\CarbonInterface;

if (!function_exists('applyPromocode')) {
    /**
     * @param string $code
     * @param Model|null $user
     * @return PromocodeContract|null
     */
    function applyPomocode(string $code, ?Model $user = null): ?PromocodeContract
    {
        $promocodes = Promocodes::code($code);

        if ($user) {
            $promocodes = $promocodes->user($user);
        }

        return $promocodes->apply();
    }
}

if (!function_exists('expirePromocode')) {
    /**
     * @param string $code
     * @return bool
     */
    function expirePromocode(string $code): bool
    {
        $promocode = app(PromocodeContract::class)->findByCode($code)->first();

        if (!$promocode) {
            throw new PromocodeDoesNotExistException($code);
        }

        return $promocode->update(['expired_at' => now()]);
    }
}

if (!function_exists('createPromocodes')) {
    /**
     * @param string|null $mask
     * @param string|null $characters
     * @param int $count
     * @param bool $unlimited
     * @param int $usages
     * @param bool $multiUse
     * @param Model|null $user
     * @param bool $boundToUser
     * @param CarbonInterface|null $expiration
     * @return Collection
     */
    function createPromocodes(?string $mask = null, ?string $characters = null, int $count = 1, bool $unlimited = false, int $usages = 1, bool $multiUse = false, ?Model $user = null, bool $boundToUser = false, ?CarbonInterface $expiration = null, array $details = []): Collection
    {
        $promocodes = Promocodes::count($count)->details($details);

        if ($mask) {
            $promocodes = $promocodes->mask($mask);
        }

        if ($characters !== null) {
            $promocodes = $promocodes->characters($characters);
        }

        if ($unlimited) {
            $promocodes = $promocodes->unlimited();
        }

        if ($usages !== null) {
            $promocodes = $promocodes->usages($usages);
        }

        if ($multiUse) {
            $promocodes = $promocodes->multiUse();
        }

        if ($user) {
            $promocodes = $promocodes->user($user);
        }

        if ($boundToUser) {
            $promocodes = $promocodes->boundToUser();
        }

        if ($expiration) {
            $promocodes = $promocodes->expiration($expiration);
        }

        return $promocodes->create();
    }
}
