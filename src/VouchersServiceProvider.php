<?php

namespace Flyzard\Vouchers;

use Illuminate\Support\ServiceProvider;

class VouchersServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'flyzard');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'flyzard');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
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
        $this->mergeConfigFrom(__DIR__.'/../config/vouchers.php', 'vouchers');

        // Register the service the package provides.
        $this->app->singleton('vouchers', function ($app) {
            return new Vouchers;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['vouchers'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/vouchers.php' => config_path('vouchers.php'),
        ], 'vouchers.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/flyzard'),
        ], 'vouchers.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/flyzard'),
        ], 'vouchers.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/flyzard'),
        ], 'vouchers.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
