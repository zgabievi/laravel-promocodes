<?php

namespace Gabievi\Promocodes\Tests;

use Promocodes;
use Gabievi\Promocodes\Models\Promocode;
use Gabievi\Promocodes\Tests\Models\User;
use Gabievi\Promocodes\Exceptions\AlreadyUsedException;

class RewardableTraitTest extends TestCase
{
    //
    public $user;

    //
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::find(1);
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_returns_null_if_could_not_apply_promocode()
    {
        $applyCode = $this->user->applyCode('INVALID-CODE');

        $this->assertNull($applyCode);
    }

    /** @test */
    public function it_returns_null_in_callback_if_could_not_apply_promocode()
    {
        $this->user->applyCode('INVALID-CODE', function ($applyCode) {
            $this->assertNull($applyCode);
        });
    }

    /** @test */
    public function it_throws_exception_if_user_already_applied_to_code()
    {
        $this->expectException(AlreadyUsedException::class);

        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        $this->user->applyCode($promocode['code']);
        $this->user->applyCode($promocode['code']);
    }

    /** @test */
    public function it_attaches_current_user_as_applied_to_promocode()
    {
        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        $this->user->applyCode($promocode['code']);

        $this->assertCount(1, $this->user->promocodes);

        $userPromocode = $this->user->promocodes()->first();

        $this->assertNotNull($userPromocode->pivot->used_at);
    }

    /** @test */
    public function is_returns_promocode_with_user_if_applied_successfuly()
    {
        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        $appliedPromocode = $this->user->applyCode($promocode['code']);

        $this->assertTrue($appliedPromocode instanceof Promocode);

        $this->assertCount(1, $appliedPromocode->users);
    }

    /** @test */
    public function is_returns_promocode_with_user_in_callback_if_applied_successfuly()
    {
        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        $this->user->applyCode($promocode['code'], function ($appliedPromocode) {
            $this->assertTrue($appliedPromocode instanceof Promocode);
        });
    }

    /** @test */
    public function it_has_alias_named_reedem_code()
    {
        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        $this->user->redeemCode($promocode['code']);

        $this->assertCount(1, $this->user->promocodes);
    }
}
