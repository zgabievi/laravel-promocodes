<?php

namespace Gabievi\Promocodes\Test;

use Promocodes;
use Gabievi\Promocodes\Models\Promocode;
use Gabievi\Promocodes\Test\Models\User;
use Gabievi\Promocodes\Exceptions\AlreadyUsedExceprion;
use Gabievi\Promocodes\Exceptions\UnauthenticatedExceprion;

class ApplyPromocodeToUserTest extends TestCase
{
    /** @test */
    public function it_throws_exception_if_user_is_not_authenticated()
    {
        $this->expectException(UnauthenticatedExceprion::class);

        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        Promocodes::apply($promocode['code']);
    }

    /** @test */
    public function it_returns_false_if_promocode_doesnt_exist()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $appliedPromocode = Promocodes::apply('INVALID-CODE');

        $this->assertFalse($appliedPromocode);
    }

    /** @test */
    public function it_throws_exception_if_user_tries_to_apply_code_twice()
    {
        $this->expectException(AlreadyUsedExceprion::class);

        $user = User::find(1);
        $this->actingAs($user);

        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        Promocodes::apply($promocode['code']);
        Promocodes::apply($promocode['code']);
    }

    /** @test */
    public function it_attaches_authenticated_user_as_applied_to_promocode()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        Promocodes::apply($promocode['code']);

        $this->assertCount(1, $user->promocodes);

        $userPromocode = $user->promocodes()->first();

        $this->assertNotNull($userPromocode->pivot->used_at);
    }

    /** @test */
    public function is_returns_promocode_with_user_if_applied_successfuly()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        $appliedPromocode = Promocodes::apply($promocode['code']);

        $this->assertTrue($appliedPromocode instanceof Promocode);

        $this->assertCount(1, $appliedPromocode->users);
    }

    /** @test */
    public function it_has_alias_named_reedem()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        Promocodes::redeem($promocode['code']);

        $this->assertCount(1, $user->promocodes);
    }
}
