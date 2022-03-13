<?php

namespace Zorb\Promocodes\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Zorb\Promocodes\Contracts\PromocodeContract;
use Zorb\Promocodes\Events\UserAppliedPromocode;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Zorb\Promocodes\Facades\Promocodes;

trait AppliesPromocode
{
    /**
     * @return BelongsToMany
     */
    public function appliedPromocodes(): BelongsToMany
    {
        return $this->belongsToMany(
            config('promocodes.models.promocodes.model'),
            config('promocodes.models.pivot.table_name'),
            config('promocodes.models.users.foreign_id'),
            config('promocodes.models.promocodes.foreign_id'),
        )
            ->using(config('promocodes.models.pivot.model'))
            ->withPivot('created_at');
    }

    /**
     * @return HasMany
     */
    public function boundPromocodes(): HasMany
    {
        return $this->hasMany(
            config('promocodes.models.promocodes.model'),
            config('promocodes.models.users.foreign_id'),
        );
    }

    /**
     * @param string $code
     * @return PromocodeContract|null
     */
    public function applyPromocode(string $code): ?PromocodeContract
    {
        return Promocodes::code($code)->user($this)->apply();
    }
}
