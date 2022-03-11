<?php

namespace Zorb\Promocodes\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait AppliesPromocodes
{
    //
    public function promocodes(): BelongsToMany
    {
        return $this->belongsToMany();
    }

    //
    public function applyPromocode(string $code): bool
    {
        return true;
    }
}
