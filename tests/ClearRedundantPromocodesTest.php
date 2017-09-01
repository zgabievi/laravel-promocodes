<?php

namespace Gabievi\Promocodes\Test;

use Promocodes;
use Gabievi\Promocodes\Models\Promocode;
use Gabievi\Promocodes\Test\Models\User;

class ClearRedundantPromocodesTest extends TestCase
{
    /** @test */
    public function it_returns_null_if_there_is_no_redundant_promocodes()
    {
        $promocodes = Promocodes::create(3);

        $this->assertCount(3, $promocodes);
        $this->assertNull(Promocodes::clearRedundant());
    }

    /** @test */
    public function it_removes_expired_or_used_promocodes_and_relations()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $disposablePromocodes = Promocodes::createDisposable(2);
        $multiusePromocodes = Promocodes::create(2);

        $this->assertCount(4, Promocode::all());

        Promocodes::redeem($disposablePromocodes->first()['code']);
        Promocodes::disable($multiusePromocodes->first()['code']);

        Promocodes::clearRedundant();

        $this->assertCount(2, Promocode::all());
    }
}
