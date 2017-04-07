<?php

namespace Tests\Unit;

use Gabievi\Promocodes\Facades\Promocodes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PromocodesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_output_number_of_codes()
    {
        $codes = Promocodes::output(10);

        $this->assertCount(10, $codes);
    }

    /** @test */
    public function it_will_output_one_code_by_default()
    {
        $code = Promocodes::output();

        $this->assertCount(1, $code);
    }

    /** @test */
    public function it_will_create_number_of_codes_into_database_with_reward_value()
    {
        $code = Promocodes::create(5, 35.65)->first();

        $this->assertDatabaseHas('promocodes', [
            'code'   => $code['code'],
            'reward' => $code['reward'],
        ]);
    }

    /** @test */
    public function it_will_be_stored_with_additional_data()
    {
        $data = [
            'baz' => 'qux',
            'foo' => 'bar',
        ];

        $code = Promocodes::create(1, 50, $data)->first();

        $this->assertDatabaseHas('promocodes', [
            'reward' => 50,
        ]);

        $this->assertEquals($code['data'], json_encode($data));
    }

    /** @test */
    public function it_returns_true_if_code_exists_in_database()
    {
        $code = Promocodes::create(1, 80)->first();
        $status = Promocodes::check($code['code']);

        $this->assertTrue($status);
    }

    /** @test */
    public function it_will_apply_given_code()
    {
        $code = Promocodes::create(20, 15)->first();

        $status = Promocodes::apply($code['code']);

        $this->assertEquals(15, $status->reward);
    }

    /** @test */
    public function it_will_apply_given_code_with_some_additional_data()
    {
        $data = [
            'baz' => 'qux',
            'foo' => 'bar',
        ];

        $code = Promocodes::create(20, 15, $data)->first();

        $status = Promocodes::apply($code['code']);

        $this->assertEquals('qux', $status->data['baz']);
    }
}
