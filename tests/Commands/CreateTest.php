<?php

use Zorb\Promocodes\Tests\Models\User;
use Zorb\Promocodes\Models\Promocode;
use Illuminate\Support\Str;

it('should create codes in database', function () {
    $this->artisan('promocodes:create', ['--count' => 3]);

    expect(Promocode::count())->toEqual(3);
});

it('should create code in database with custom mask', function () {
    $this->artisan('promocodes:create', ['--mask' => 'FOO-***-BAR']);

    $promocode = Promocode::first();

    expect(Str::startsWith($promocode->code, 'FOO-'))->toBeTrue();
    expect(Str::endsWith($promocode->code, '-BAR'))->toBeTrue();
    expect(Str::length($promocode->code))->toEqual(11);
});

it('should create code in database with custom characters', function () {
    $this->artisan('promocodes:create', ['--mask' => '***', '--characters' => '0']);

    $promocode = Promocode::first();

    expect($promocode->code)->toEqual('000');
});

it('should create code in database with user bounding', function () {
    $this->artisan('promocodes:create', ['--bound-to-user' => true]);

    $promocode = Promocode::first();

    expect($promocode->bound_to_user)->toBeTrue();
});

it('should create code in database with unlimited usages', function () {
    $this->artisan('promocodes:create', ['--unlimited' => true]);

    $promocode = Promocode::first();

    expect($promocode->usages_left)->toEqual(-1);
});

it('should create code in database with multi use', function () {
    $this->artisan('promocodes:create', ['--multi-use' => true]);

    $promocode = Promocode::first();

    expect($promocode->multi_use)->toBeTrue();
});

it('should create code in database with custom usages left', function () {
    $this->artisan('promocodes:create', ['--usages' => 5]);

    $promocode = Promocode::first();

    expect($promocode->usages_left)->toEqual(5);
});

it('should create code in database with expiration', function () {
    $this->artisan('promocodes:create', ['--expiration' => '2022-01-01 00:00:00']);

    $promocode = Promocode::first();

    expect($promocode->expired_at->year)->toEqual(2022);
    expect($promocode->expired_at->month)->toEqual(1);
    expect($promocode->expired_at->day)->toEqual(1);
});

it('should create code in database with user association', function () {
    $user = User::factory()->create();

    $this->artisan('promocodes:create', ['--user' => $user->id]);

    $promocode = Promocode::first();

    expect($promocode->user->id)->toEqual($user->id);
});

it('should return error if user is not found', function () {
    $this->artisan('promocodes:create', ['--user' => 1])->assertExitCode(1);
});
