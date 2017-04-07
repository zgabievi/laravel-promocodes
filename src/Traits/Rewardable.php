<?php

namespace Gabievi\Promocodes\Traits;

use Gabievi\Promocodes\Facades\Promocodes;
use Gabievi\Promocodes\Model\Promocode;

trait Rewardable
{
    /**
     * Create promocodes for current model.
     *
     * @param int   $amount
     * @param null  $reward
     * @param array $data
     *
     * @return mixed
     */
    public function createCode($amount = 1, $reward = null, array $data = [])
    {
        $records = [];

        // loop though each promocodes required
        foreach (Promocodes::output($amount) as $code) {
            $records[] = new Promocode([
                'code'   => $code,
                'reward' => $reward,
                'data' => json_encode($data),
            ]);
        }

        // check for insertion of record
        if ($this->promocodes()->saveMany($records)) {
            return collect($records);
        }

        return collect([]);
    }

    /**
     * Apply promocode for user and get callback.
     *
     * @param $code
     * @param $callback
     *
     * @return bool|float
     */
    public function applyCode($code, $callback = null)
    {
        $promocode = Promocode::byCode($code)->fresh()->first();

        // check if exists not used code
        if (!is_null($promocode)) {

            //
            if (!is_null($promocode->user) && $promocode->user->id !== $this->attributes['id']) {

                // callback function with false value
                if (is_callable($callback)) {
                    $callback(false);
                }

                return false;
            }

            // update promocode as it is used
            if ($promocode->update(['is_used' => true])) {

                // callback function with promocode model
                if (is_callable($callback)) {
                    $callback($promocode ?: true);
                }

                return $promocode ?: true;
            }
        }

        // callback function with false value
        if (is_callable($callback)) {
            $callback(false);
        }

        return false;
    }
}
