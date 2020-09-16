<?php

namespace Gabievi\Promocodes\Traits;

use Carbon\Carbon;
use Gabievi\Promocodes\Models\Promocode;
use Gabievi\Promocodes\Facades\Promocodes;
use Gabievi\Promocodes\Exceptions\AlreadyUsedException;

trait Rewardable
{
    /**
     * Get the promocodes that are related to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function promocodes()
    {
        return $this->belongsToMany(Promocode::class, config('promocodes.relation_table'))
            ->withPivot('used_at');
    }

    /**
     * Redeem promocode to user and get callback.
     *
     * @param string $code
     * @param null|\Closure $callback
     *
     * @return null|\Gabievi\Promocodes\Models\Promocode
     * @throws AlreadyUsedException
     */
    public function redeemCode($code, $callback = null)
    {
        return $this->applyCode($code, $callback);
    }

    /**
     * Apply promocode to user and get callback.
     *
     * @param string $code
     * @param null|\Closure $callback
     *
     * @return bool|null|\Gabievi\Promocodes\Models\Promocode
     * @throws AlreadyUsedException
     */
    public function applyCode($code, $callback = null)
    {
        if ($promocode = Promocodes::check($code)) {
            if ($promocode->isDisposable() && $promocode->users()->wherePivot(config('promocodes.related_pivot_key'), $this->id)->exists()) {
                throw new AlreadyUsedException;
            }

            $promocode->users()->attach($this->id, [
                config('promocodes.foreign_pivot_key') => $promocode->id,
                'used_at' => Carbon::now(),
            ]);

            if (!is_null($promocode->quantity)) {
                $promocode->quantity -= 1;
                $promocode->save();
            }

            $promocode->load('users');

            if (is_callable($callback)) {
                $callback($promocode);
            }

            return $promocode;
        }

        if (is_callable($callback)) {
            $callback(null);
        }

        return false;
    }
}
