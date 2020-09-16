<?php

namespace Gabievi\Promocodes\Tests;

use Promocodes;

class OutputRandomPromocodesTest extends TestCase
{
    /** @test */
    public function it_will_output_only_one_code_without_parameter()
    {
        $promocodes = Promocodes::output();

        $this->assertCount(1, $promocodes);
    }

    /** @test */
    public function it_can_output_several_promocodes_as_array()
    {
        $promocodes = Promocodes::output(10);

        $this->assertCount(10, $promocodes);
    }

    /** @test */
    public function it_can_output_several_promocodes_with_prefix_as_array()
    {
        $promocodes = Promocodes::setPrefix('TEST')->output(10);

        $this->assertCount(10, $promocodes);
        $this->assertStringStartsWith('TEST', $promocodes[0]);
        $this->assertStringStartsWith('TEST', $promocodes[9]);
    }

    /** @test */
    public function it_can_output_several_promocodes_with_suffix_as_array()
    {
        $promocodes = Promocodes::setSuffix('END')->output(10);

        $this->assertCount(10, $promocodes);
        $this->assertStringEndsWith('END', $promocodes[0]);
        $this->assertStringEndsWith('END', $promocodes[9]);
    }

    /** @test */
    public function it_can_output_several_promocodes_with_prefix_and_suffix_as_array()
    {
        $promocodes = Promocodes::setPrefix('ABC')->setSuffix('XYZ')->output(10);

        $this->assertCount(10, $promocodes);
        $this->assertStringStartsWith('ABC', $promocodes[0]);
        $this->assertStringStartsWith('ABC', $promocodes[9]);
        $this->assertStringEndsWith('XYZ', $promocodes[0]);
        $this->assertStringEndsWith('XYZ', $promocodes[9]);
    }
}
