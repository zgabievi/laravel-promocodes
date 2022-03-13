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
        $this->publishes([
            __DIR__ . '/../config/promocodes.php' => config_path('promocodes.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/create_promocodes_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hi') . '00_create_promocodes_table.php'),
            __DIR__ . '/../database/migrations/create_promocode_user_table.php.stub' => database_path('migrations/' . date('Y_m_d_Hi') . '01_create_promocode_user_table.php'),
        ], 'migrations');

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
}
