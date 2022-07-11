<?php

namespace Zorb\Promocodes\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface PromocodeContract
{
    //
    public function user(): BelongsTo;

    //
    public function users(): BelongsToMany;

    //
    public function scopeAvailable(Builder $builder): void;

    //
    public function scopeFindByCode(Builder $builder, string $code): Builder;

    //
    public function isExpired(): bool;

    //
    public function isUnlimited(): bool;

    //
    public function hasUsagesLeft(): bool;

    //
    public function allowedForUser(Model $user): bool;
}
