<?php

use Zorb\Promocodes\Models\PromocodeUser;
use Zorb\Promocodes\Tests\Models\{User, Currency};
use Zorb\Promocodes\Models\Promocode;

it('should apply promocode with given code', function () {
    $code = 'ABC-DEF';
    $currency = Currency::factory()->create();
    Promocode::factory()->code($code)->currency($currency->id)->notExpired()->boundToUser(false)->usagesLeft(2)->create();

    applyPomocode($code, $currency);

    expect(PromocodeUser::count())->toEqual(1);
    expect(PromocodeUser::first()->user_id)->toBeNull();
});

it('should apply promocode with given code for given user', function () {
    $code = 'ABC-DEF';
    $user = User::factory()->create();
    $currency = Currency::factory()->create();
    Promocode::factory()->code($code)->currency($currency->id)->notExpired()->boundToUser(false)->usagesLeft(2)->create();

    applyPomocode($code, $currency, $user);

    expect(PromocodeUser::count())->toEqual(1);
    expect(PromocodeUser::first()->user_id)->toEqual($user->id);
});

