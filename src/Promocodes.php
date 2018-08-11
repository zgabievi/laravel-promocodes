<?php

namespace Gabievi\Promocodes;

use Carbon\Carbon;
use Gabievi\Promocodes\Models\Promocode;
use Gabievi\Promocodes\Exceptions\AlreadyUsedException;
use Gabievi\Promocodes\Exceptions\UnauthenticatedException;
use Gabievi\Promocodes\Exceptions\InvalidPromocodeException;

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

            array_push($collection, $random);
        }

        return $collection;
    }

    /**
     * Save promocodes into database
     * Successful insert returns generated promocodes
     * Fail will return empty collection.
     *
     * @param int $amount
     * @param null $reward
     * @param array $data
     * @param int|null $expires_in
     * @param bool $is_disposable
     *
     * @return \Illuminate\Support\Collection
     */
    public function create($amount = 1, $reward = null, array $data = [], $expires_in = null, $is_disposable = false)
    {
        $records = [];

        foreach ($this->output($amount) as $code) {
            $records[] = [
                'code' => $code,
                'reward' => $reward,
                'data' => json_encode($data),
                'expires_at' => $expires_in ? Carbon::now()->addDays($expires_in) : null,
                'is_disposable' => $is_disposable,
            ];
        }

        if (Promocode::insert($records)) {
            return collect($records)->map(function ($record) {
                $record['data'] = json_decode($record['data'], true);
                return $record;
            });
        }

        return collect([]);
    }

    /**
     * Save one-time use promocodes into database
     * Successful insert returns generated promocodes
     * Fail will return empty collection.
     *
     * @param int $amount
     * @param null $reward
     * @param array $data
     * @param int|null $expires_in
     *
     * @return \Illuminate\Support\Collection
     */
    public function createDisposable($amount = 1, $reward = null, array $data = [], $expires_in = null)
    {
        return $this->create($amount, $reward, $data, $expires_in, true);
    }

    /**
     * Check promocode in database if it is valid.
     *
     * @param string $code
     *
     * @return bool|\Gabievi\Promocodes\Model\Promocode
     * @throws \Gabievi\Promocodes\Exceptions\InvalidPromocodeException
     */
    public function check($code)
    {
        $promocode = Promocode::byCode($code)->first();

        if ($promocode === null) {
            throw new InvalidPromocodeException;
        }

        if ($promocode->isExpired()) {
            return false;
        }

        return $promocode;
    }

    /**
     * Apply promocode to user that it's used from now.
     *
     * @param string $code
     *
     * @return bool|\Gabievi\Promocodes\Model\Promocode
     * @throws \Gabievi\Promocodes\Exceptions\UnauthenticatedException|\Gabievi\Promocodes\Exceptions\AlreadyUsedException
     */
    public function apply($code)
    {
        if (!auth()->check()) {
            throw new UnauthenticatedException;
        }

        try {
            if ($promocode = $this->check($code)) {
                if ($this->isSecondUsageAttempt($promocode)) {
                    throw new AlreadyUsedException;
                }

                $promocode->users()->attach(auth()->id(), [
                    'used_at' => Carbon::now(),
                ]);

                return $promocode->load('users');
            }
        } catch (InvalidPromocodeException $exception) {
            //
        }

        return false;
    }

    /**
     * Reedem promocode to user that it's used from now.
     *
     * @param string $code
     *
     * @return bool|\Gabievi\Promocodes\Model\Promocode
     * @throws \Gabievi\Promocodes\Exceptions\UnauthenticatedException|\Gabievi\Promocodes\Exceptions\AlreadyUsedException
     */
    public function redeem($code)
    {
        return $this->apply($code);
    }

    /**
     * Expire code as it won't usable anymore.
     *
     * @param string $code
     * @return bool
     * @throws \Gabievi\Promocodes\Exceptions\InvalidPromocodeException
     */
    public function disable($code)
    {
        $promocode = Promocode::byCode($code)->first();

        if ($promocode === null) {
            throw new InvalidPromocodeException;
        }

        $promocode->expires_at = Carbon::now();

        return $promocode->save();
    }

    /**
     * Clear all expired and used promotion codes
     * that can not be used anymore.
     *
     * @return void
     */
    public function clearRedundant()
    {
        Promocode::all()->each(function ($promocode) {
            if ($promocode->isExpired() || ($promocode->isDisposable() && $promocode->users()->exists())) {
                $promocode->users()->detach();
                $promocode->delete();
            }
        });
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

        for ($i = 1; $i <= $this->length; $i++) {
            $character = $characters[rand(0, strlen($characters) - 1)];
            $random[] = $character;
        }

        shuffle($random);
        $length = count($random);

        $promocode .= $this->getPrefix();

        for ($i = 0; $i < $length; $i++) {
            $mask = preg_replace('/\*/', $random[$i], $mask, 1);
        }

        $promocode .= $mask;
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
        return (bool)config('promocodes.prefix')
            ? config('promocodes.prefix') . config('promocodes.separator')
            : '';
    }

    /**
     * Generate suffix with separator for promocode.
     *
     * @return string
     */
    private function getSuffix()
    {
        return (bool)config('promocodes.suffix')
            ? config('promocodes.separator') . config('promocodes.suffix')
            : '';
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
     * Check if user is trying to apply code again.
     *
     * @param $promocode
     *
     * @return bool
     */
    private function isSecondUsageAttempt($promocode) {
        return $promocode->users()->wherePivot('user_id', auth()->id())->exists();
    }
}
