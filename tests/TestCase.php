<?php

namespace Gabievi\Promocodes\Tests;

use Gabievi\Promocodes\Tests\Models\User;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    //
    public function setUp()
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'sqlite']);

        $this->seedUsers();

        $this->setUpDatabase();

        $this->updateConfig();
    }

    //
    protected function getPackageProviders($app)
    {
        return [
            \Gabievi\Promocodes\PromocodesServiceProvider::class
        ];
    }

    //
    protected function getPackageAliases($app)
    {
        return [
            'Promocodes' => \Gabievi\Promocodes\Facades\Promocodes::class,
        ];
    }

    //
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);


        $app['config']->set('app.key', 'dwFcFNf8J3fJ3RYADQbWMHyNx8YK');
    }

    //
    protected function setUpDatabase()
    {
        include_once __DIR__.'/../migrations/2016_05_17_221000_create_promocodes_table.php';

        (new \CreatePromocodesTable)->up();
    }

    //
    protected function seedUsers()
    {
        User::create([
            'name' => 'user',
            'email' => 'user@example.com',
            'password' => 'secret'
        ]);
    }

    //
    protected function updateConfig()
    {
        $this->app['config']->set('promocodes.user_model', User::class);
    }
}
