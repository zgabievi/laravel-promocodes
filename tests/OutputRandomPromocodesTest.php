<?php

namespace Gabievi\Promocodes\Test;

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
}
