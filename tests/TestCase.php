<?php

namespace Zorb\Promocodes\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Session;
use Orchestra\Testbench\TestCase as Orchestra;
use Zorb\Promocodes\PromocodesServiceProvider;
use Illuminate\Support\Facades\Schema;
use Zorb\Promocodes\Tests\Models\User;
use Zorb\Promocodes\Tests\Models\Currency;
use Illuminate\Database\Schema\Blueprint;

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
        $application['config']->set('promocodes.models.currency.model', Currency::class);
    }

    //
    public function setUpDatabase()
    {
        $config = include __DIR__ . '/../config/promocodes.php';

        Schema::dropIfExists($config['models']['promocodes']['table_name']);
        Schema::dropIfExists($config['models']['pivot']['table_name']);
        Schema::dropIfExists('users');
        Schema::dropIfExists('currencies');

        if (!class_exists(\CreatePromocodesTable::class)) {
            include __DIR__ . '/../database/migrations/create_promocodes_table.php.stub';
        }

        if (!class_exists(\CreatePromocodeUserTable::class)) {
            include __DIR__ . '/../database/migrations/create_promocode_user_table.php.stub';
        }
        

        if (!class_exists(\AddFieldToPromocodesTable::class)) {
            include __DIR__ . '/../database/migrations/add_field_to_promocodes_table.php.stub';
        }

        if (!class_exists(\AddMinPriceToPromocodesTable::class)) {
            include __DIR__ . '/../database/migrations/add_min_price_to_promocodes_table.php.stub';
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id('id');
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->id('id');
            $table->string('value');
            $table->timestamps();
        });

        (new \CreatePromocodesTable)->up();
        (new \CreatePromocodeUserTable)->up();
        (new \AddFieldToPromocodesTable)->up();
        (new \AddMinPriceToPromocodesTable)->up();
    }
}
