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
     * Prefix for code generation
     *
     * @var string
     */
    protected $prefix;

    /**
     * Suffix for code generation
     *
     * @var string
     */
    protected $suffix;

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

        $this->prefix = (bool)config('promocodes.prefix')
            ? config('promocodes.prefix') . config('promocodes.separator')
            : '';

        $this->suffix = (bool)config('promocodes.suffix')
            ? config('promocodes.separator') . config('promocodes.suffix')
            : '';
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
     * @param int|null $quantity
     *
     * @return \Illuminate\Support\Collection
     */
    public function createDisposable(
        $amount = 1,
        $reward = null,
        array $data = [],
        $expires_in = null,
        $quantity = null
    )
    {
        return $this->create($amount, $reward, $data, $expires_in, $quantity, true);
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
     * @param int|null $quantity
     *
     * @return \Illuminate\Support\Collection
     */
    public function create(
        $amount = 1,
        $reward = null,
        array $data = [],
        $expires_in = null,
        $quantity = null,
        $is_disposable = false
    )
    {
        $records = [];

        foreach ($this->output($amount) as $code) {
            $records[] = [
                'code' => $code,
                'reward' => $reward,
                'data' => json_encode($data),
                'expires_at' => $expires_in ? Carbon::now()->addDays($expires_in) : null,
                'is_disposable' => $is_disposable,
                'quantity' => $quantity,
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
     * Set custom prefix for next generation
     *
     * @param $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Set custom suffix for next generation
     *
     * @param $suffix
     * @return $this
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
        return $this;
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

        $promocode .= $this->prefix;

        for ($i = 0; $i < $length; $i++) {
            $mask = preg_replace('/\*/', $random[$i], $mask, 1);
        }

        $promocode .= $mask;
        $promocode .= $this->suffix;

        return $promocode;
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
     * Reedem promocode to user that it's used from now.
     *
     * @param string $code
     *
     * @return bool|Promocode
     * @throws AlreadyUsedException
     * @throws UnauthenticatedException
     */
    public function redeem($code)
    {
        return $this->apply($code);
    }

    /**
     * Apply promocode to user that it's used from now.
     *
     * @param string $code
     *
     * @return bool|Promocode
     * @throws AlreadyUsedException
     * @throws UnauthenticatedException
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
                    'promocode_id' => $promocode->id,
                    'used_at' => Carbon::now(),
                ]);

                if (!is_null($promocode->quantity)) {
                    $promocode->quantity -= 1;
                    $promocode->save();
                }

                return $promocode->load('users');
            }
        } catch (InvalidPromocodeException $exception) {
            //
        }

        return false;
    }

    /**
     * Check promocode in database if it is valid.
     *
     * @param string $code
     *
     * @return bool|Promocode
     * @throws InvalidPromocodeException
     */
    public function check($code)
    {
        $promocode = Promocode::byCode($code)->first();

        if ($promocode === null) {
            throw new InvalidPromocodeException;
        }

        if ($promocode->isExpired() || ($promocode->isDisposable() && $promocode->users()->exists()) || $promocode->isOverAmount()) {
            return false;
        }

        return $promocode;
    }

    /**
     * Check if user is trying to apply code again.
     *
     * @param Promocode $promocode
     *
     * @return bool
     */
    public function isSecondUsageAttempt(Promocode $promocode)
    {
        return $promocode->users()->wherePivot(config('promocodes.related_pivot_key', 'user_id'),
            auth()->id())->exists();
    }

    /**
     * Expire code as it won't usable anymore.
     *
     * @param string $code
     * @return bool
     * @throws InvalidPromocodeException
     */
    public function disable($code)
    {
        $promocode = Promocode::byCode($code)->first();

        if ($promocode === null) {
            throw new InvalidPromocodeException;
        }

        $promocode->expires_at = Carbon::now();
        $promocode->quantity = 0;

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
        Promocode::all()->each(function (Promocode $promocode) {
            if ($promocode->isExpired() || ($promocode->isDisposable() && $promocode->users()->exists()) || $promocode->isOverAmount()) {
                $promocode->users()->detach();
                $promocode->delete();
            }
        });
    }

    /**
     * Get the list of valid promocodes
     *
     * @return Promocode[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Promocode::all()->filter(function (Promocode $promocode) {
            return !$promocode->isExpired() && !($promocode->isDisposable() && $promocode->users()->exists()) && !$promocode->isOverAmount();
        });
    }
}
