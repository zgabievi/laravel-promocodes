<?php

namespace Zorb\Promocodes;

use Zorb\Promocodes\Contracts\PromocodeUserContract;
use Zorb\Promocodes\Contracts\PromocodeContract;
use Illuminate\Support\ServiceProvider;
use Zorb\Promocodes\Commands\Expire;
use Zorb\Promocodes\Commands\Create;
use Zorb\Promocodes\Commands\Apply;

class PromocodesServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerModelBindings();

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/promocodes.php', 'promocodes');

        $this->app->singleton('promocodes', function ($app) {
            return new Promocodes;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return ['promocodes'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        if (!$this->configExists('promocodes')) {
            $this->publishes([
                __DIR__ . '/../config/promocodes.php' => config_path('promocodes.php'),
            ], 'config');
        }

        if (!$this->migrationExists('create_promocodes_table')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_promocodes_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hi') . '00_create_promocodes_table.php'),
            ], 'migrations');
        }

        if (!$this->migrationExists('create_promocode_user_table')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_promocode_user_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hi') . '01_create_promocode_user_table.php'),
            ], 'migrations');
        }

        if (!$this->migrationExists('add_field_to_promocodes_table')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/add_field_to_promocodes_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hi') . '02_add_field_to_promocodes_table.php'),
            ], 'migrations');
        }
        if (!$this->migrationExists('add_min_price_to_promocodes_table')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/add_min_price_to_promocodes_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hi') . '03_add_min_price_to_promocodes_table.php'),
            ], 'migrations');
        }

        $this->commands([Apply::class, Expire::class, Create::class]);
    }

    /**
     * @return void
     */
    protected function registerModelBindings(): void
    {
        $config = $this->app->config['promocodes']['models'];

        $this->app->bind(PromocodeContract::class, $config['promocodes']['model']);
        $this->app->bind(PromocodeUserContract::class, $config['pivot']['model']);
    }

    /**
     * @return bool
     */
    protected function migrationExists($mgr) : bool
    {
        $path = database_path('migrations/');
        $files = scandir($path);
        $pos = false;
        foreach ($files as &$value) {
            $pos = strpos($value, $mgr);

            if ($pos !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function configExists($config) : bool
    {
        $path = config_path();
        $files = scandir($path);
        $pos = false;
        foreach ($files as &$value) {
            $pos = strpos($value, $config);
            
            if ($pos !== false) {
                return true;
            }
        }
        return false;
    }
}
