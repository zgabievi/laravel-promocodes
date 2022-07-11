<?php

namespace Zorb\Promocodes\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Zorb\Promocodes\Contracts\PromocodeContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Promocode extends Model implements PromocodeContract
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id', 'code', 'usages_left', 'bound_to_user', 'multi_use', 'details', 'expired_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expired_at' => 'datetime',
        'usages_left' => 'integer',
        'bound_to_user' => 'boolean',
        'multi_use' => 'boolean',
        'details' => 'json',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('promocodes.models.promocodes.table_name'));
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            config('promocodes.models.users.model'),
            config('promocodes.models.users.foreign_id'),
        );
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            config('promocodes.models.users.model'),
            config('promocodes.models.pivot.table_name'),
            config('promocodes.models.promocodes.foreign_id'),
            config('promocodes.models.users.foreign_id'),
        )
            ->using(config('promocodes.models.pivot.model'))
            ->withPivot('created_at', 'session_id');
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeAvailable(Builder $builder): void
    {
        $builder->whereNull('expired_at')->orWhere('expired_at', '>', now());
    }

    /**
     * @param Builder $builder
     * @param string $code
     * @return Builder
     */
    public function scopeFindByCode(Builder $builder, string $code): Builder
    {
        return $builder->where('code', $code);
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isBefore(now());
    }

    /**
     * @return bool
     */
    public function isUnlimited(): bool
    {
        return $this->usages_left === -1;
    }

    /**
     * @return bool
     */
    public function hasUsagesLeft(): bool
    {
        return $this->isUnlimited() || $this->usages_left > 0;
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function allowedForUser(Model $user): bool
    {
        return !$this->bound_to_user || $this->user === null || $this->user->is($user);
    }

    /**
     * @param Model $user
     * @return bool
     */
    public function appliedByUser(Model $user): bool
    {
        return $this->users()->where(DB::raw('users.id'), $user->id)->exists();
    }

    /**
     * @param string $key
     * @param mixed|null $fallback
     * @return mixed
     */
    public function getDetail(string $key, mixed $fallback = null): mixed
    {
        return $this->details[$key] ?? $fallback;
    }
}
