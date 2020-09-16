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
     * Number of codes to be generated
     *
     * @var int
     */
    protected $amount = 1;

    /**
     * Reward value which will be sticked to code
     *
     * @var null
     */
    protected $reward = null;

    /**
     * Additional data to be returned with code
     *
     * @var array
     */
    protected $data = [];

    /**
     * Number of days of code expiration
     *
     * @var null|int
     */
    protected $expires_in = null;

    /**
     * Maximum number of available usage of code
     *
     * @var null|int
     */
    protected $quantity = null;

    /**
     * If code should automatically invalidate after first use
     *
     * @var bool
     */
    protected $disposable = false;

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
        $amount = null,
        $reward = null,
        $data = null,
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
        $amount = null,
        $reward = null,
        $data = null,
        $expires_in = null,
        $quantity = null,
        $is_disposable = null
    )
    {
        $records = [];

        foreach ($this->output($amount) as $code) {
            $records[] = [
                'code' => $code,
                'reward' => $this->getReward($reward),
                'data' => json_encode($this->getData($data)),
                'expires_at' => $this->getExpiresIn($expires_in) ? Carbon::now()->addDays($this->getExpiresIn($expires_in)) : null,
                'is_disposable' => $this->getDisposable($is_disposable),
                'quantity' => $this->getQuantity($quantity),
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
    public function output($amount = null)
    {
        $collection = [];

        for ($i = 1; $i <= $this->getAmount($amount); $i++) {
            $random = $this->generate();

            while (!$this->validate($collection, $random)) {
                $random = $this->generate();
            }

            array_push($collection, $random);
        }

        return $collection;
    }

    /**
     * Get number of codes to be generated
     *
     * @param null|int $request
     * @return null|int
     */
    public function getAmount($request)
    {
        return $request !== null ? $request : $this->amount;
    }

    /**
     * Set how much code you want to be generated
     *
     * @param int $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
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
     * Get custom set reward value
     *
     * @param null|int $request
     * @return null|int
     */
    public function getReward($request)
    {
        return $request !== null ? $request : $this->reward;
    }

    /**
     * Set custom reward value
     *
     * @param int $reward
     * @return $this
     */
    public function setReward($reward)
    {
        $this->reward = $reward;
        return $this;
    }

    /**
     * Get custom set data value
     *
     * @param null|array $data
     * @return null|array
     */
    public function getData($request)
    {
        return $request !== null ? $request : $this->data;
    }

    /**
     * Set custom data value
     *
     * @param array $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get custom set expiration days value
     *
     * @param null|int $request
     * @return null|int
     */
    public function getExpiresIn($request)
    {
        return $request !== null ? $request : $this->expires_in;
    }

    /**
     * Set custom expiration days value
     *
     * @param int $expires_in
     * @return $this
     */
    public function setExpiresIn($expires_in)
    {
        $this->expires_in = $expires_in;
        return $this;
    }

    /**
     * Get custom disposable value
     *
     * @param null|bool $request
     * @return null|bool
     */
    public function getDisposable($request)
    {
        return $request !== null ? $request : $this->disposable;
    }

    /**
     * Set custom disposable value
     *
     * @param bool $disposable
     * @return $this
     */
    public function setDisposable($disposable = true)
    {
        $this->disposable = $disposable;
        return $this;
    }

    /**
     * Get custom set quantity value
     *
     * @param null|int $request
     * @return null|int
     */
    public function getQuantity($request)
    {
        return $request !== null ? $request : $this->quantity;
    }

    /**
     * Set custom quantity value
     *
     * @param int $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Set custom prefix for next generation
     *
     * @param string $prefix
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
     * @param string $suffix
     * @return $this
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
        return $this;
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

        return false;
    }

    /**
     * Check promocode in database if it is valid.
     *
     * @param string $code
     *
     * @return bool|Promocode
     */
    public function check($code)
    {
        $promocode = Promocode::byCode($code)->first();

        if ($promocode === null || $promocode->isExpired() || ($promocode->isDisposable() && $promocode->users()->exists()) || $promocode->isOverAmount()) {
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
        return $promocode->isDisposable() && $promocode->users()->wherePivot(config('promocodes.related_pivot_key', 'user_id'),
                auth()->id())->exists();
    }

    /**
     * Expire code as it won't be usable anymore.
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
