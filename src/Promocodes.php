<?php

namespace Gabievi\Promocodes;

use Gabievi\Promocodes\Model\Promocode;

class Promocodes
{
    /**
     * Generated codes will be saved here
     * to be validated later.
     *
     * @var array
     */
    private $codes = [];

    /**
     * Length of code will be calculated from asterisks you have
     * set as mask in your config file.
     *
     * @var int
     */
    private $length;

    /**
     * Promocodes constructor.
     */
    public function __construct()
    {
        $this->codes = Promocode::pluck('code')->toArray();
        $this->length = substr_count(config('promocodes.mask'), '*');
    }

    /**
     * Here will be generated single code using your parameters from config.
     *
     * @return string
     */
    private function generate()
    {
        $characters = config('promocodes.characters');
        $mask = config('promocodes.mask');
        $promocode = '';
        $random = [];

        // take needed length of string from characters and randomize it
        for ($i = 1; $i <= $this->length; $i++) {
            $character = $characters[rand(0, strlen($characters) - 1)];
            $random[] = $character;
        }

        // shuffle randomized characters
        shuffle($random);

        // set prefix for promocode
        $promocode .= $this->getPrefix();

        // loop through asterisks and change with random symbol
        for ($i = 0; $i < count($random); $i++) {
            $mask = preg_replace('/\*/', $random[$i], $mask, 1);
        }

        // set updated mask as code
        $promocode .= $mask;

        // set suffix for promocode
        $promocode .= $this->getSuffix();

        return $promocode;
    }

    /**
     * Generate prefix with separator for promocode.
     *
     * @return string
     */
    private function getPrefix()
    {
        return (bool) config('promocodes.prefix') ? config('promocodes.prefix').config('promocodes.separator') : '';
    }

    /**
     * Generate suffix with separator for promocode.
     *
     * @return string
     */
    private function getSuffix()
    {
        return (bool) config('promocodes.suffix') ? config('promocodes.separator').config('promocodes.suffix') : '';
    }

    /**
     * Your code will be validated to be unique for one request.
     *
     * @param $collection
     * @param $new
     *
     * @return bool
     */
    private function validate($collection, $new)
    {
        return !in_array($new, array_merge($collection, $this->codes));
    }

    /**
     * Generates promocodes as many as you wish.
     *
     * @param int $amount
     *
     * @return array
     */
    public function output($amount = 1)
    {
        $collection = [];

        for ($i = 1; $i <= $amount; $i++) {
            $random = $this->generate();

            while (!$this->validate($collection, $random)) {
                $random = $this->generate();
            }

            $collection[] = $random;
        }

        return $collection;
    }

    /**
     * Save promocodes into database
     * Successful insert returns generated promocodes
     * Fail will return NULL.
     *
     * @param int  $amount
     * @param null $reward
     *
     * @return static
     */
    public function create($amount = 1, $reward = null)
    {
        $records = [];

        // loop though each promocodes required
        foreach ($this->output($amount) as $code) {
            $records[] = [
                'code'   => $code,
                'reward' => $reward,
            ];
        }

        // check for insertion of record
        if (Promocode::insert($records)) {
            return collect($records);
        }

        return collect([]);
    }

    /**
     * Check promocode in database if it is valid.
     *
     * @param $code
     *
     * @return bool
     */
    public function check($code)
    {
        return Promocode::byCode($code)->fresh()->exists();
    }

    /**
     * Apply promocode to user that it's used from now.
     *
     * @param $code
     *
     * @return bool|float
     */
    public function apply($code)
    {
        $promocode = Promocode::byCode($code)->fresh();

        // check if exists not used code
        if ($promocode->exists()) {
            $record = $promocode->first();
            $record->is_used = true;

            // update promocode as it is used
            if ($record->save()) {
                return $record->reward ?: true;
            }
        }

        return false;
    }
}
