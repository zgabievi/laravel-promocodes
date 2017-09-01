<?php

namespace Gabievi\Promocodes\Test;

use Promocodes;

class ConfigurationTest extends TestCase
{
    /** @test */
    public function it_generates_code_with_predefinded_characters_only()
    {
        $this->app['config']->set('promocodes.characters', '1234567890');

        $promocodes = Promocodes::output();
        $promocode = $promocodes[0];

        $this->assertRegExp('/^[0-9]/', $promocode);
    }

    /** @test */
    public function it_generates_code_with_prefix()
    {
        $this->app['config']->set('promocodes.prefix', 'foo');

        $promocodes = Promocodes::output();
        $promocode = $promocodes[0];

        $this->assertStringStartsWith('foo', $promocode);
    }

    /** @test */
    public function it_generates_code_with_suffix()
    {
        $this->app['config']->set('promocodes.suffix', 'bar');

        $promocodes = Promocodes::output();
        $promocode = $promocodes[0];

        $this->assertStringEndsWith('bar', $promocode);
    }

    /** @test */
    public function it_generates_code_with_mask()
    {
        $this->app['config']->set('promocodes.mask', '* * * *');

        $promocodes = Promocodes::output();
        $promocode = $promocodes[0];

        $this->assertRegExp('/(.*)\s(.*)\s(.*)\s(.*)/', $promocode);
    }

    /** @test */
    public function it_has_underscore_as_code_separator_for_prefix_and_suffix()
    {
        $this->app['config']->set('promocodes.prefix', 'foo');
        $this->app['config']->set('promocodes.suffix', 'bar');
        $this->app['config']->set('promocodes.separator', '_');

        $promocodes = Promocodes::output();
        $promocode = $promocodes[0];

        $this->assertStringStartsWith('foo_', $promocode);
        $this->assertStringEndsWith('_bar', $promocode);
    }
}
