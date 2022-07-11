<?php

namespace Zorb\Promocodes;

use Zorb\Promocodes\Exceptions\PromocodeAlreadyUsedByUserException;
use Zorb\Promocodes\Exceptions\PromocodeBoundToOtherUserException;
use Zorb\Promocodes\Exceptions\UserHasNoAppliesPromocodeTrait;
use Zorb\Promocodes\Exceptions\PromocodeDoesNotExistException;
use Zorb\Promocodes\Exceptions\PromocodeNoUsagesLeftException;
use Zorb\Promocodes\Exceptions\UserRequiredToAcceptPromocode;
use Zorb\Promocodes\Exceptions\PromocodeExpiredException;
use Zorb\Promocodes\Contracts\PromocodeUserContract;
use Zorb\Promocodes\Events\GuestAppliedPromocode;
use Zorb\Promocodes\Events\UserAppliedPromocode;
use Zorb\Promocodes\Contracts\PromocodeContract;
use Zorb\Promocodes\Traits\AppliesPromocode;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Carbon\CarbonInterface;

class Promocodes
{
    /**
     * @var string|null
     */
    protected ?string $code = null;

    /**
     * @var string|null
     */
    protected ?string $mask = null;

    /**
     * @var string|null
     */
    protected ?string $characters = null;

    /**
     * @var bool
     */
    protected bool $boundToUser = false;

    /**
     * @var int
     */
    protected int $count = 1;

    /**
     * @var bool
     */
    protected bool $unlimited = false;

    /**
     * @var bool
     */
    protected bool $multiUse = false;

    /**
     * @var array
     */
    protected array $details = [];

    /**
     * @var int
     */
    protected int $usagesLeft = 1;

    /**
     * @var CarbonInterface|null
     */
    protected ?CarbonInterface $expiredAt = null;

    /**
     * @var Model|null
     */
    protected ?Model $user = null;

    /**
     * @var PromocodeContract|null
     */
    protected ?PromocodeContract $promocode = null;

    /**
     * @param string $code
     * @return $this
     */
    public function code(string $code): static
    {
        $promocodeModel = app(PromocodeContract::class);
        $promocode = $promocodeModel->findByCode($code)->first();

        $this->code = $code;
        $this->promocode = $promocode;
        return $this;
    }

    /**
     * @param Model $user
     * @return $this
     */
    public function user(Model $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param string $mask
     * @return $this
     */
    public function mask(string $mask): static
    {
        $this->mask = $mask;
        return $this;
    }

    /**
     * @param string $characters
     * @return $this
     */
    public function characters(string $characters): static
    {
        $this->characters = $characters;
        return $this;
    }

    /**
     * @param bool $boundToUser
     * @return $this
     */
    public function boundToUser(bool $boundToUser = true): static
    {
        $this->boundToUser = $boundToUser;
        return $this;
    }

    /**
     * @param bool $multiUse
     * @return $this
     */
    public function multiUse(bool $multiUse = true): static
    {
        $this->multiUse = $multiUse;
        return $this;
    }

    /**
     * @param bool $unlimited
     * @return $this
     */
    public function unlimited(bool $unlimited = true): static
    {
        $this->unlimited = $unlimited;
        return $this;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function count(int $count): static
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @param int $usagesLeft
     * @return $this
     */
    public function usages(int $usagesLeft): static
    {
        $this->usagesLeft = $usagesLeft;
        return $this;
    }

    /**
     * @param array $details
     * @return $this
     */
    public function details(array $details): static
    {
        $this->details = $details;
        return $this;
    }

    /**
     * @param CarbonInterface $expiredAt
     * @return $this
     */
    public function expiration(CarbonInterface $expiredAt): static
    {
        $this->expiredAt = $expiredAt;
        return $this;
    }

    /**
     * @return PromocodeContract|null
     */
    public function apply(): ?PromocodeContract
    {
        if (!$this->promocode) {
            throw new PromocodeDoesNotExistException($this->code);
        }

        if (!$this->promocode->hasUsagesLeft()) {
            throw new PromocodeNoUsagesLeftException($this->code);
        }

        if ($this->promocode->isExpired()) {
            throw new PromocodeExpiredException($this->code);
        }

        if ($this->promocode->bound_to_user && !$this->user) {
            throw new UserRequiredToAcceptPromocode($this->code);
        }

        if ($this->user) {
            if (!in_array(AppliesPromocode::class, class_uses($this->user), true)) {
                throw new UserHasNoAppliesPromocodeTrait();
            }

            if (!$this->promocode->allowedForUser($this->user)) {
                throw new PromocodeBoundToOtherUserException($this->user, $this->code);
            }

            if (!$this->promocode->multi_use && $this->promocode->appliedByUser($this->user)) {
                throw new PromocodeAlreadyUsedByUserException($this->user, $this->code);
            }

            $this->user->appliedPromocodes()->attach($this->promocode, ['session_id' => Session::getId()]);

            if ($this->promocode->bound_to_user && $this->promocode->user_id === null) {
                $this->promocode->user()->associate($this->user);
                $this->promocode->save();
            }

            event(new UserAppliedPromocode($this->promocode, $this->user));
        } else {
            $models = config('promocodes.models');
            $promocodeForeignId = $models['promocodes']['foreign_id'];

            $attributes = [];
            $attributes[$promocodeForeignId] = $this->promocode->id;
            $attributes['session_id'] = Session::getId();

            $promocodeUserModel = app(PromocodeUserContract::class);
            $promocodeUserModel->forceCreate($attributes);

            event(new GuestAppliedPromocode($this->promocode));
        }

        $this->promocode->decrement('usages_left');

        return $this->promocode;
    }

    /**
     * @return Collection
     */
    public function create(): Collection
    {
        return $this->generate()->map(fn(string $code) => app(PromocodeContract::class)->create([
            'user_id' => optional($this->user)->id,
            'code' => $code,
            'usages_left' => $this->unlimited ? -1 : $this->usagesLeft,
            'bound_to_user' => $this->user || $this->boundToUser,
            'multi_use' => $this->multiUse,
            'details' => $this->details,
            'expired_at' => $this->expiredAt,
        ]));
    }

    /**
     * @return Collection
     */
    public function generate(): Collection
    {
        $existingCodes = app(PromocodeContract::class)->pluck('code')->toArray();
        $codes = collect([]);

        for ($i = 1; $i <= $this->count; $i++) {
            $code = $this->generateCode();

            while ($this->codeExists($code, $existingCodes)) {
                $code = $this->generateCode();
            }

            $codes->push($code);
            $existingCodes = array_merge($existingCodes, [$code]);
        }

        return $codes;
    }

    /**
     * @return string
     */
    protected function generateCode(): string
    {
        $characters = $this->characters ?? config('promocodes.allowed_symbols');
        $mask = $this->mask ?? config('promocodes.code_mask');
        $maskLength = substr_count($mask, '*');
        $randomCharacter = [];

        for ($i = 1; $i <= $maskLength; $i++) {
            $character = $characters[rand(0, strlen($characters) - 1)];
            $randomCharacter[] = $character;
        }

        shuffle($randomCharacter);
        $length = count($randomCharacter);

        for ($i = 0; $i < $length; $i++) {
            $mask = preg_replace('/\*/', $randomCharacter[$i], $mask, 1);
        }

        return $mask;
    }

    /**
     * @param string $code
     * @param array<string> $existingCodes
     * @return bool
     */
    protected function codeExists(string $code, array $existingCodes): bool
    {
        return in_array($code, $existingCodes, true);
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return app(PromocodeContract::class)->all();
    }

    /**
     * @return Collection
     */
    public function available(): Collection
    {
        return app(PromocodeContract::class)->whereNot('usages_left', 0)->where(function ($query) {
            $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
        })->get();
    }

    /**
     * @return Collection
     */
    public function notAvailable(): Collection
    {
        return app(PromocodeContract::class)->where('usages_left', 0)->orWhere('expired_at', '<=', now())->get();
    }
}
