<?php

namespace Gabievi\Promocodes\Tests;

use Promocodes;
use Gabievi\Promocodes\Models\Promocode;
use Gabievi\Promocodes\Exceptions\InvalidPromocodeException;

class DisablePromocodeTest extends TestCase
{
    /** @test */
    public function it_thows_exception_if_promocode_is_invalid()
    {
        $this->expectException(InvalidPromocodeException::class);

        Promocodes::disable('INVALID-CODE');
    }

    /** @test */
    public function it_sets_expiration_date_if_code_was_valid()
    {
        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        Promocodes::disable($promocode['code']);
        $dbPromocode = Promocode::byCode($promocode['code'])->first();

        $this->assertNotNull($dbPromocode->expires_at);
    }

    /** @test */
    public function it_returns_true_if_disabled()
    {
        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $disabledPromocode = Promocodes::disable($promocode['code']);

        $this->assertTrue($disabledPromocode);
    }
}
