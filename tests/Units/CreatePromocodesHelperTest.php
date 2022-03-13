<?php

use Zorb\Promocodes\Tests\Models\User;
use Zorb\Promocodes\Models\Promocode;
use Illuminate\Support\Str;
use Carbon\Carbon;

it('should create codes in database', function () {
    createPromocodes(count: 3);

    expect(Promocode::count())->toEqual(3);
});

it('should create code in database with custom mask', function () {
    createPromocodes(mask: 'FOO-***-BAR');

    $promocode = Promocode::first();

    expect(Str::startsWith($promocode->code, 'FOO-'))->toBeTrue();
    expect(Str::endsWith($promocode->code, '-BAR'))->toBeTrue();
    expect(Str::length($promocode->code))->toEqual(11);
});

it('should create code in database with custom characters', function () {
    createPromocodes(mask: '***', characters: '0');

    $promocode = Promocode::first();

    expect($promocode->code)->toEqual('000');
});

it('should create code in database with user bounding', function () {
    createPromocodes(boundToUser: true);

    $promocode = Promocode::first();

    expect($promocode->bound_to_user)->toBeTrue();
});

it('should create code in database with unlimited usages', function () {
    createPromocodes(unlimited: true);

    $promocode = Promocode::first();

    expect($promocode->usages_left)->toEqual(-1);
});

it('should create code in database with multi use', function () {
    createPromocodes(multiUse: true);

    $promocode = Promocode::first();

    expect($promocode->multi_use)->toBeTrue();
});

it('should create code in database with custom usages left', function () {
    createPromocodes(usages: 5);

    $promocode = Promocode::first();

    expect($promocode->usages_left)->toEqual(5);
});

it('should create code in database with expiration', function () {
    $expiration = Carbon::create(2022, 0, 0);
    createPromocodes(expiration: $expiration);

    $promocode = Promocode::first();

    expect($promocode->expired_at->year)->toEqual($expiration->year);
    expect($promocode->expired_at->month)->toEqual($expiration->month);
    expect($promocode->expired_at->day)->toEqual($expiration->day);
});

it('should create code in database with user association', function () {
    $user = User::factory()->create();
    createPromocodes(user: $user);

    $promocode = Promocode::first();

    expect($promocode->user->id)->toEqual($user->id);
});

it('should create code in database with details', function () {
    createPromocodes(details: ['foo' => 'bar']);

    $promocode = Promocode::first();

    expect($promocode->details['foo'])->toEqual('bar');
});

