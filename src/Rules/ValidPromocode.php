<?php

namespace Zorb\Promocodes\Rules;

use Illuminate\Contracts\Validation\Rule;
use Zorb\Promocodes\Models\Promocode;

class ValidPromocode implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return Promocode::where('code', $value)->whereNot('usages_left', 0)->where(function ($query) {
            $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
        })->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('Invalid promotional code.');
    }
}
