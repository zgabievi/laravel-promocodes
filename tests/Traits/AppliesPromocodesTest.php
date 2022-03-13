<?php

use Zorb\Promocodes\Contracts\PromocodeContract;
use Zorb\Promocodes\Facades\Promocodes;
use Zorb\Promocodes\Models\Promocode;
use Zorb\Promocodes\Tests\Models\User;

it('should return promocodes applied by user', function () {
    $user = User::factory()->create();
    $promocodes = Promocode::factory()->count(3)->notExpired()->usagesLeft(1)->create();

    Promocodes::user($user)->code($promocodes->first()->code)->apply();
    Promocodes::user($user)->code($promocodes->last()->code)->apply();

    expect($user->appliedPromocodes()->count())->toEqual(2);
});

it('should return promocodes bound to user', function () {
    $user = User::factory()->create();

    Promocodes::user($user)->count(3)->create();

    expect($user->boundPromocodes()->count())->toEqual(3);
});

it('should apply promocode to user', function () {
    $user = User::factory()->create();
    $code = 'ABC-DEF';
    Promocode::factory()->code($code)->notExpired()->usagesLeft(1)->create();

    expect($user->applyPromocode($code))->toBeInstanceOf(PromocodeContract::class);
});
