<?php

namespace Gabievi\Promocodes;

use Illuminate\Support\Facades\DB;

class Promocodes
{

	/**
     * Generated codes will be saved here
     * to be validated later
     * 
	 * @var array
	 */
	protected $codes = [];

	/**
     * Length of code will be calculated
     * from asterisks you have set as
     * mas in your config file
     *
	 * @var int
	 */
	protected $length;

	/**
     * Model of promocodes from your config
     * will be saved in this variable
     *
	 * @var Model
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
     * Here will be generated single code
     * using your parameters from config
     *
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
     * Your code will be validted to
     * be unique for one request
     *
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
     * Generates promocodes as many as you wish
     *
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
	 * Save promocodes into database
     * Successfull insert returns generated promocodes
     * Fail will return NULL
	 *
	 * @param int $amount
	 *
	 * @return static
	 */
	public function save($amount = 1)
	{
		$data = [];

		foreach ($this->generate($amount) as $key => $code) {
			$data[]['code'] = $code;
		}

        // if insertion goes well
        if ($this->model->insert($data)) {
            return collect($data);
        } else {
            return null;
        }
	}

	/**
     * Check promocode in database if it is valid
     *
	 * @param $code
	 *
	 * @return bool
	 */
	public function check($code)
	{
		return $this->model->where('code', $code)->where('is_used', false)->count() > 0;
	}

	/**
     * Apply pomocode to user that it's used from now
     *
	 * @param $code
	 *
	 * @return bool
	 */
	public function apply($code)
	{
		$row = $this->model->where('code', $code)->where('is_used', false);

		if ($row->count() > 0) {
			$record = $row->first();
			$record->is_used = true;
			return $record->save();
		}

		return false;
	}
}