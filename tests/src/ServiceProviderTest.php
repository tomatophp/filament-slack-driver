<?php

use Illuminate\Support\Facades\Artisan;
use TomatoPHP\FilamentSlackDriver\FilamentSlackDriverServiceProvider;

it('boots the service provider', function () {
    expect(app()->getProviders(FilamentSlackDriverServiceProvider::class))->not->toBeEmpty();
});

it('merges the package config', function () {
    expect(config()->has('filament-slack-driver'))->toBeTrue()
        ->and(config('filament-slack-driver'))->toBeArray();
});

it('registers the install command', function () {
    expect(Artisan::all())->toHaveKey('filament-slack-driver:install');
});
