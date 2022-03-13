<?php

use Zorb\Promocodes\Models\Promocode;

it('should return available promocodes', function () {
    Promocode::factory()->expired()->count(5)->create();
    Promocode::factory()->notExpired()->count(5)->create();

    expect(Promocode::available()->count())->toEqual(5);
});

it('should read passed details', function () {
    $promocode = Promocode::factory()->details([
        'reward' => 500,
    ])->create();

    expect($promocode->getDetail('reward'))->toEqual(500);
});

it('should return fallback for detail', function () {
    $promocode = Promocode::factory()->details([])->create();

    expect($promocode->getDetail('reward', 300))->toEqual(300);
});
