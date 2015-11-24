<?php

namespace zgabievi\Promocodes;

use Illuminate\Support\Facades\DB;

class Promocodes
{

	/**
	 * @var array
	 */
	protected $codes = [];

	/**
	 * @var
	 */
	protected $length;

	/**
	 * @var
	 */
	protected $model;

	/**
	 * Promocodes constructor.
	 */
	public function __construct()
	{
		$this->model = app()->make(config('promocodes.model'));

		$this->codes  = $this->model->lists('code')->toArray();
		$this->length = substr_count(config('promocodes.mask'), '*');
	}

	/**
	 * @return string
	 */
	public function randomize()
	{
		$characters = config('promocodes.characters');
		$separator  = config('promocodes.separator');
		$mask       = config('promocodes.mask');
		$prefix     = config('promocodes.prefix');
		$suffix     = config('promocodes.suffix');

		$random = [];
		$code   = '';

		for ($i = 1; $i <= $this->length; $i++) {
			$character = $characters[rand(0, strlen($characters) - 1)];
			$random[]  = $character;
		}

		shuffle($random);

		if ($prefix !== false) {
			$code .= $prefix . $separator;
		}

		for ($i = 0; $i < count($random); $i++) {
			$mask = preg_replace('/\*/', $random[$i], $mask, 1);
		}

		$code .= $mask;

		if ($suffix !== false) {
			$code .= $separator . $suffix;
		}

		return $code;
	}

	/**
	 * @param $collection
	 * @param $new
	 *
	 * @return bool
	 */
	public function validate($collection, $new)
	{
		if (count($collection) == 0 && count($this->codes) == 0) return true;

		$combined = array_merge($collection, $this->codes);

		return !in_array($new, $combined);
	}

	/**
	 * @param int $amount
	 *
	 * @return array
	 */
	public function generate($amount = 1)
	{
		$collection = [];

		for ($i = 1; $i <= $amount; $i++) {
			$random = $this->randomize();

			while (!$this->validate($collection, $random)) {
				$random = $this->randomize();
			}

			$collection[] = $random;
		}

		return $collection;
	}

	/**
	 * save into database
	 *
	 * @param int   $amount
	 *
	 * @return static
	 */
	public function save($amount = 1)
	{
		$data = [];

		foreach ($this->generate($amount) as $key => $code) {
			$data[]['code'] = $code;
		}

		return $this->model->insert($data);
	}
}