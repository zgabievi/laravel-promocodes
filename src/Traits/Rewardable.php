<?php

namespace Gabievi\Promocodes\Traits;

use Carbon\Carbon;
use Gabievi\Promocodes\Model\Promocode;
use Gabievi\Promocodes\Facades\Promocodes;

trait Rewardable
{
    /**
     * Get the promocodes that are related to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function promocodes()
    {
        return $this->belongsToMany(Promocode::class, config('promocodes.relation_table'));
    }

    /**
     * Apply promocode to user and get callback.
     *
     * @param string $code
     * @param null|\Closure $callback
     *
     * @return null|\Gabievi\Promocodes\Model\Promocode
     * @throws \Gabievi\Promocodes\Exceptions\AlreadyUsedExceprion
     */
    public function applyCode($code, $callback = null)
    {
        if ($promocode = Promocodes::check($code)) {
            if ($promocode->users()->wherePivot('user_id', $this->id)->exists()) {
                throw new AlreadyUsedExceprion;
            }

            $promocode->users()->attach($this->id, [
                'used_at' => Carbon::now(),
            ]);

            $promocode->load('users');

            if (is_callable($callback)) {
                $callback($promocode);
            }

            return $promocode;
        }

        if (is_callable($callback)) {
            $callback(null);
        }

        return null;
    }
}
