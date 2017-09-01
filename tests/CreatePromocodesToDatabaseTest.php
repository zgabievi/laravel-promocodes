<?php

namespace Gabievi\Promocodes\Test;

use Promocodes;

class CreatePromocodesToDatabaseTest extends TestCase
{
    /** @test */
    public function it_will_create_only_one_code_without_parameters()
    {
        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);
        $this->assertDatabaseHas('promocodes', [
            'code' => $promocode['code']
        ]);
    }

    /** @test */
    public function it_can_create_several_promocodes_and_save_in_database()
    {
        $promocodes = Promocodes::create(10);
        $firstPromocode = $promocodes->first();
        $lastPromocode = $promocodes->last();

        $this->assertCount(10, $promocodes);
        $this->assertDatabaseHas('promocodes', [
            'code' => $firstPromocode['code']
        ]);
        $this->assertDatabaseHas('promocodes', [
            'code' => $lastPromocode['code']
        ]);
    }

    /** @test */
    public function it_can_set_reward_value_to_promocodes()
    {
        $promocodes = Promocodes::create(1, 10);
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);
        $this->assertEquals(10, $promocode['reward']);
        $this->assertDatabaseHas('promocodes', [
            'code' => $promocode['code'],
            'reward' => $promocode['reward']
        ]);
    }

    /** @test */
    public function it_can_set_additional_data_to_promocodes()
    {
        $data = [
            'foo' => 'bar',
            'baz' => 'qux',
        ];

        $promocodes = Promocodes::create(1, null, $data);
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);
        $this->assertDatabaseHas('promocodes', [
            'code' => $promocode['code'],
            'data' => json_encode($data),
        ]);
        $this->assertEquals('bar', $promocode['data']['foo']);
    }

    /** @test */
    public function it_can_set_days_to_expire_promocode()
    {
        $promocodes = Promocodes::create(1, null, [], 5);
        $promocode = $promocodes->first();

        $expires_at = date('Y-m-d H:i:s', strtotime('+5 days'));

        $this->assertCount(1, $promocodes);
        $this->assertDatabaseHas('promocodes', [
            'code' => $promocode['code'],
            'expires_at' => $expires_at,
        ]);
    }

    /** @test */
    public function it_will_create_multiuse_promocode_by_deafult()
    {
        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);
        $this->assertDatabaseHas('promocodes', [
            'code' => $promocode['code'],
            'is_disposable' => false,
        ]);
    }

    /** @test */
    public function it_can_create_disposable_promocode()
    {
        $promocodes = Promocodes::createDisposable();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);
        $this->assertDatabaseHas('promocodes', [
            'code' => $promocode['code'],
            'is_disposable' => true,
        ]);
    }
}
