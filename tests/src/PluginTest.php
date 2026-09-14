<?php

use Filament\Facades\Filament;
use TomatoPHP\FilamentSlackDriver\FilamentSlackDriverPlugin;

it('makes the plugin with its id', function () {
    expect(FilamentSlackDriverPlugin::make())
        ->toBeInstanceOf(FilamentSlackDriverPlugin::class)
        ->getId()->toBe('filament-slack-driver');
});

it('registers the plugin on the panel', function () {
    expect(Filament::getPanel('admin')->getPlugin('filament-slack-driver'))
        ->toBeInstanceOf(FilamentSlackDriverPlugin::class);
});
