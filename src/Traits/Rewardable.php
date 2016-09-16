<?php

namespace Gabievi\Promocodes\Traits;

use Gabievi\Promocodes\Facades\Promocodes;
use Gabievi\Promocodes\Model\Promocode;

trait Rewardable
{
	/**
	 * Get the promocodes of current model.
	 *
	 * @return mixed
	 */
	public function promocodes()
	{
		return $this->hasMany(Promocode::class);
	}
	
	/**
	 * Create promocodes for current model.
	 *
	 * @param int $amount
	 * @param null $reward
	 * @return mixed
	 */
	public function promocode($amount = 1, $reward = null)
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
	 */
	public function applyCode($code, $callback)
	{
		$promocode = $this->promocodes()->byCode($code)->fresh();
		
		// check if exists not used code
		if ($promocode->exists()) {
			$record = $promocode->first();
			$record->is_used = true;
			
			// update promocode as it is used
			if ($record->save()) {
				
				// callback function with reward value
				if (is_callable($callback)) {
					$callback($record->reward ?: true);
				}
				
				return $record->reward ?: true;
			}
		}
		
		// callback function with false value
		if (is_callable($callback)) {
			$callback(false);
		}
		
		return false;
	}
}