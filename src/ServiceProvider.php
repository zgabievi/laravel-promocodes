<?php

namespace Gabievi\Promocodes;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/promocodes.php' => config_path('promocodes.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../migrations' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/promocodes.php', 'promocodes'
        );

        $this->app->singleton('promocodes', function ($app) {
            return new Promocodes();
        });
    }
}
