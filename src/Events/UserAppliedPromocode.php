<?php

namespace Zorb\Promocodes\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Zorb\Promocodes\Contracts\PromocodeContract;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class UserAppliedPromocode
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PromocodeContract
     */
    public PromocodeContract $promocode;

    /**
     * @var Model
     */
    public Model $user;

    /**
     * @param PromocodeContract $promocode
     * @param Model $user
     */
    public function __construct(PromocodeContract $promocode, Model $user)
    {
        $this->promocode = $promocode;
        $this->user = $user;
    }
}
