<?php

namespace Zorb\Promocodes\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Session;
use Orchestra\Testbench\TestCase as Orchestra;
use Zorb\Promocodes\PromocodesServiceProvider;
use Illuminate\Support\Facades\Schema;
use Zorb\Promocodes\Tests\Models\User;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations('sqlite');

        $this->setUpDatabase();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Zorb\\Promocodes\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    /**
     * @return string[]
     */
    public function getPackageProviders($application): array
    {
        return [
            PromocodesServiceProvider::class,
        ];
    }

    //
    protected function getEnvironmentSetUp($application)
    {
        Session::setId('0a4d55a8d778e5022fab701977c5d840bbc486d0');

        $application['config']->set('app.key', 'dwFcFNf8J3fJ3RYADQbWMHyNx8YK');
        $application['config']->set('promocodes.models.users.model', User::class);
    }

    //
    public function setUpDatabase()
    {
        $config = include __DIR__ . '/../config/promocodes.php';

        Schema::dropIfExists($config['models']['promocodes']['table_name']);
        Schema::dropIfExists($config['models']['pivot']['table_name']);

        if (!class_exists(\CreatePromocodesTable::class)) {
            include __DIR__ . '/../database/migrations/create_promocodes_table.php.stub';
        }

        if (!class_exists(\CreatePromocodeUserTable::class)) {
            include __DIR__ . '/../database/migrations/create_promocode_user_table.php.stub';
        }

        (new \CreatePromocodesTable)->up();
        (new \CreatePromocodeUserTable)->up();
    }
}
