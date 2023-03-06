<?php

use Zorb\Promocodes\Tests\Models\{User, Currency};
use Zorb\Promocodes\Models\Promocode;
use Illuminate\Support\Str;

it('should create codes in database', function () {
    $currency = Currency::factory()->create();

    $this->artisan('promocodes:create', ['--count' => 3, '--currency' => $currency->id]);

    expect(Promocode::count())->toEqual(3);
});

it('should create code in database with custom mask', function () {
    $currency = Currency::factory()->create();
    $this->artisan('promocodes:create', ['--mask' => 'FOO-***-BAR', '--currency' => $currency->id]);

    $promocode = Promocode::first();

    expect(Str::startsWith($promocode->code, 'FOO-'))->toBeTrue();
    expect(Str::endsWith($promocode->code, '-BAR'))->toBeTrue();
    expect(Str::length($promocode->code))->toEqual(11);
});

it('should create code in database with custom characters', function () {
    $currency = Currency::factory()->create();
    $this->artisan('promocodes:create', ['--mask' => '***', '--characters' => '0', '--currency' => $currency->id]);

    $promocode = Promocode::first();

    expect($promocode->code)->toEqual('000');
});

it('should create code in database with user bounding', function () {
    $currency = Currency::factory()->create();
    $this->artisan('promocodes:create', ['--bound-to-user' => true, '--currency' => $currency->id]);

    $promocode = Promocode::first();

    expect($promocode->bound_to_user)->toBeTrue();
});

it('should create code in database with unlimited usages', function () {
    $currency = Currency::factory()->create();
    $this->artisan('promocodes:create', ['--unlimited' => true, '--currency' => $currency->id]);

    $promocode = Promocode::first();

    expect($promocode->usages_left)->toEqual(-1);
});

it('should create code in database with multi use', function () {
    $currency = Currency::factory()->create();
    $this->artisan('promocodes:create', ['--multi-use' => true, '--currency' => $currency->id]);

    $promocode = Promocode::first();

    expect($promocode->multi_use)->toBeTrue();
});

it('should create code in database with custom usages left', function () {
    $currency = Currency::factory()->create();
    $this->artisan('promocodes:create', ['--usages' => 5, '--currency' => $currency->id]);

    $promocode = Promocode::first();

    expect($promocode->usages_left)->toEqual(5);
});

it('should create code in database with expiration', function () {
    $currency = Currency::factory()->create();
    $this->artisan('promocodes:create', ['--expiration' => '2022-01-01 00:00:00', '--currency' => $currency->id]);

    $promocode = Promocode::first();

    expect($promocode->expired_at->year)->toEqual(2022);
    expect($promocode->expired_at->month)->toEqual(1);
    expect($promocode->expired_at->day)->toEqual(1);
});

it('should create code in database with user association and currency', function () {
    $user = User::factory()->create();
    $currency = Currency::factory()->create();
    $this->artisan('promocodes:create', ['--user' => $user->id, '--currency' => $currency->id]);

    $promocode = Promocode::first();

    expect($promocode->user->id)->toEqual($user->id);
    expect($promocode->currency->id)->toEqual($currency->id);
});

it('should return error if user is not found', function () {
    $currency = Currency::factory()->create();
    $this->artisan('promocodes:create', ['--user' => 1, '--currency' => $currency->id])->assertExitCode(1);
});

it('should return error if currency is not found', function () {
    $this->artisan('promocodes:create', ['--currency' => 1])->assertExitCode(1);
});
