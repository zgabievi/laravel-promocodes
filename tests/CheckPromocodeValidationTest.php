<?php

namespace Gabievi\Promocodes\Tests;

use Promocodes;
use Gabievi\Promocodes\Models\Promocode;
use Gabievi\Promocodes\Tests\Models\User;

class CheckPromocodeValidationTest extends TestCase
{
    /** @test */
    public function it_returns_false_if_promocode_is_invalid()
    {
        $checkPromocode = Promocodes::check('INVALID-CODE');

        $this->assertFalse($checkPromocode);
    }

    /** @test */
    public function it_returns_false_if_promocode_is_expired()
    {
        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        Promocode::byCode($promocode['code'])->update([
            'expires_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ]);

        $this->assertCount(1, $promocodes);

        $checkPromocode = Promocodes::check($promocode['code']);

        $this->assertFalse($checkPromocode);
    }

    /** @test */
    public function it_returns_false_if_promocode_is_disposable_and_used()
    {
        $promocodes = Promocodes::createDisposable();
        $promocode = $promocodes->first();

        $promocode = Promocode::byCode($promocode['code'])->first();
        $user = User::find(1);

        $promocode->users()->attach($user->id, [
            config('promocodes.foreign_pivot_key', 'promocode_id') => $promocode->id,
            'used_at' => date('Y-m-d H:i:s'),
        ]);

        $this->assertCount(1, $promocodes);
        $this->assertCount(1, $user->promocodes);

        $checkPromocode = Promocodes::check($promocode['code']);

        $this->assertFalse($checkPromocode);
    }

    /** @test */
    public function it_returns_promocode_model_if_validation_passes()
    {
        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        $checkPromocode = Promocodes::check($promocode['code']);

        $this->assertTrue($checkPromocode instanceof Promocode);
        $this->assertEquals($promocode['code'], $checkPromocode->code);
    }

    /** @test */
    public function it_returns_false_if_promocode_exceeds_quantity()
    {
        $promocodes = Promocodes::create(1, null, [], null, 2);
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        $this->actingAs(User::find(1));
        $appliedPromocode = Promocodes::apply($promocode['code']);
        $this->assertNotFalse($appliedPromocode);

        $this->actingAs(User::find(2));
        $appliedPromocode = Promocodes::apply($promocode['code']);
        $this->assertNotFalse($appliedPromocode);

        $this->actingAs(User::find(3));
        $appliedPromocode = Promocodes::apply($promocode['code']);
        $this->assertFalse($appliedPromocode);
    }
}
