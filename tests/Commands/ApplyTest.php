<?php

use Zorb\Promocodes\Models\PromocodeUser;
use Zorb\Promocodes\Tests\Models\User;
use Zorb\Promocodes\Models\Promocode;

it('should create promocode-user association', function () {
    $code = 'ABC-DEF';
    $user = User::factory()->create();
    $promocode = Promocode::factory()->code($code)->notExpired()->usagesLeft(2)->create();

    $this->artisan('promocodes:apply', ['code' => $code, '--user' => $user->id]);

    expect($promocode->users()->first()->id)->toEqual($user->id);
});

it('should create promocode-guest association', function () {
    $code = 'ABC-DEF';
    Promocode::factory()->code($code)->notExpired()->boundToUser(false)->usagesLeft(2)->create();

    $this->artisan('promocodes:apply', ['code' => $code]);

    expect(PromocodeUser::count())->toEqual(1);
    expect(PromocodeUser::first()->user_id)->toBeNull();
});

it('should return error if user is not found', function () {
    $code = 'FOO-BAR';
    $this->artisan('promocodes:apply', ['code' => $code, '--user' => 1])->assertExitCode(1);
});
