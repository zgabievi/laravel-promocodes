<?php

namespace Zorb\Promocodes\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Zorb\Promocodes\Contracts\PromocodeContract;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GuestAppliedPromocode
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PromocodeContract
     */
    public PromocodeContract $promocode;

    /**
     * @param PromocodeContract $promocode
     */
    public function __construct(PromocodeContract $promocode)
    {
        $this->promocode = $promocode;
    }
}
