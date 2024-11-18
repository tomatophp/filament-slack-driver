<?php

namespace TomatoPHP\FilamentSlackDriver;

use Illuminate\Support\ServiceProvider;


class FilamentSlackDriverServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //Register generate command
        $this->commands([
           \TomatoPHP\FilamentSlackDriver\Console\FilamentSlackDriverInstall::class,
        ]);
 
        //Register Config file
        $this->mergeConfigFrom(__DIR__.'/../config/filament-slack-driver.php', 'filament-slack-driver');
 
        //Publish Config
        $this->publishes([
           __DIR__.'/../config/filament-slack-driver.php' => config_path('filament-slack-driver.php'),
        ], 'filament-slack-driver-config');
 
        //Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
 
        //Publish Migrations
        $this->publishes([
           __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'filament-slack-driver-migrations');
        //Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-slack-driver');
 
        //Publish Views
        $this->publishes([
           __DIR__.'/../resources/views' => resource_path('views/vendor/filament-slack-driver'),
        ], 'filament-slack-driver-views');
 
        //Register Langs
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filament-slack-driver');
 
        //Publish Lang
        $this->publishes([
           __DIR__.'/../resources/lang' => base_path('lang/vendor/filament-slack-driver'),
        ], 'filament-slack-driver-lang');
 
        //Register Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
 
    }

    public function boot(): void
    {
        //you boot methods here
    }
}
