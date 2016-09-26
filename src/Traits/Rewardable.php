<?php

namespace Gabievi\Promocodes\Traits;

use Gabievi\Promocodes\Facades\Promocodes;
use Gabievi\Promocodes\Model\Promocode;

trait Rewardable
{
	
	/**
	 * Create promocodes for current model.
	 *
	 * @param int $amount
	 * @param null $reward
	 * @return mixed
	 */
	public function createCode($amount = 1, $reward = null)
	{
		$records = [];
		
		// loop though each promocodes required
		foreach (Promocodes::output($amount) as $code) {
			$records[] = new Promocode([
				'code' => $code,
				'reward' => $reward
			]);
		}
		
		// check for insertion of record
		if ($insert = $this->promocodes()->saveMany($records)) {
			return collect($records);
		}
		
		return null;
	}
	
	/**
	 * Apply promocode for user and get callback
	 *
	 * @param $code
	 * @param $callback
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
				if (is_callable($callback)) $callback(false);
				return false;
			}
			
			// update promocode as it is used
			if ($promocode->update(['is_used' => true])) {
				
				// callback function with reward value
				if (is_callable($callback)) $callback($promocode->reward ?: true);
				return $promocode->reward ?: true;
			}
		}
		
		// callback function with false value
		if (is_callable($callback)) $callback(false);
		return false;
	}
}