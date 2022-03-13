<?php

use Zorb\Promocodes\Models\PromocodeUser;
use Zorb\Promocodes\Tests\Models\User;
use Zorb\Promocodes\Models\Promocode;

it('should apply promocode with given code', function () {
    $code = 'ABC-DEF';
    Promocode::factory()->code($code)->notExpired()->boundToUser(false)->usagesLeft(2)->create();

    applyPomocode($code);

    expect(PromocodeUser::count())->toEqual(1);
    expect(PromocodeUser::first()->user_id)->toBeNull();
});

it('should apply promocode with given code for given user', function () {
    $code = 'ABC-DEF';
    $user = User::factory()->create();
    Promocode::factory()->code($code)->notExpired()->boundToUser(false)->usagesLeft(2)->create();

    applyPomocode($code, $user);

    expect(PromocodeUser::count())->toEqual(1);
    expect(PromocodeUser::first()->user_id)->toEqual($user->id);
});

