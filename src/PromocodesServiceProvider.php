<?php

namespace Gabievi\Promocodes;

use Illuminate\Support\ServiceProvider;

class PromocodesServiceProvider extends ServiceProvider
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

        if (!class_exists('CreatePromocodesTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../migrations/create_promocodes_table.php.stub' => database_path("/migrations/{$timestamp}_create_promocodes_table.php"),
            ], 'migrations');
        }
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
