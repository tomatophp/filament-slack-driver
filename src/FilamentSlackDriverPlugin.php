<?php

namespace TomatoPHP\FilamentSlackDriver;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentSlackDriverPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-slack-driver';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): self
    {
        return new FilamentSlackDriverPlugin;
    }
}
