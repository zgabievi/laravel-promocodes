<?php

namespace Gabievi\Promocodes\Tests;

use Gabievi\Promocodes\Models\Promocode;
use Gabievi\Promocodes\Tests\Models\User;
use Illuminate\Support\Facades\Schema;
use Promocodes;

class CustomMigrationConfigurationTest extends TestCase
{
    protected $promocodeInstance;

    public function setUp(): void
    {
        parent::setUp();

        $this->promocodeInstance = new Promocode;
    }

    protected function updateConfig()
    {
        $this->app['config']->set('promocodes.user_model', User::class);

        $this->app['config']->set('promocodes.table', 'coupons');
        $this->app['config']->set('promocodes.relation_table', 'coupon_user');
        $this->app['config']->set('promocodes.foreign_pivot_key', 'coupon_id');
        $this->app['config']->set('promocodes.related_pivot_key', 'user_id');
    }

    /** @test */
    public function it_migrates_with_custom_config_values()
    {
        $this->assertTrue(Schema::hasTable('coupons'));
        $this->assertTrue(Schema::hasTable('coupon_user'));
        $this->assertTrue(Schema::hasColumns('coupon_user', ['coupon_id', 'user_id']));
    }

    /** @test */
    public function it_can_create_code_with_custom_migration_config_values_in_database()
    {
        Promocodes::create();

        $this->assertCount(1, Promocodes::all());
    }

    /** @test */
    public function it_can_reedem_code_with_custom_migration_config_values()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $promocodes = Promocodes::create();
        $promocode = $promocodes->first();

        $this->assertCount(1, $promocodes);

        Promocodes::redeem($promocode['code']);

        $this->assertCount(1, $user->promocodes);
    }
}
