<?php

namespace TomatoPHP\FilamentSlackDriver;

use Filament\Contracts\Plugin;
use Filament\Panel;
use TomatoPHP\FilamentAlerts\Facades\FilamentAlerts;
use TomatoPHP\FilamentAlerts\Services\Concerns\NotificationDriver;
use TomatoPHP\FilamentSettingsHub\Facades\FilamentSettingsHub;
use TomatoPHP\FilamentSettingsHub\Services\Contracts\SettingHold;
use TomatoPHP\FilamentSlackDriver\Filament\Pages\SlackSettingsPage;
use TomatoPHP\FilamentSlackDriver\Services\SlackDriver;

class FilamentSlackDriverPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-slack-driver';
    }

    public function register(Panel $panel): void
    {
        if (class_exists(FilamentSettingsHub::class) && $panel->getPlugin('filament-alerts')->useSettingsHub) {
            $panel->pages([
                SlackSettingsPage::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        if (class_exists(FilamentSettingsHub::class) && filament('filament-alerts')->useSettingsHub) {
            FilamentSettingsHub::register([
                SettingHold::make()
                    ->label('filament-slack-driver::messages.settings.slack.title')
                    ->icon('bxl-slack')
                    ->page(SlackSettingsPage::class)
                    ->order(2)
                    ->description('filament-slack-driver::messages.settings.slack.description')
                    ->group('filament-alerts::messages.settings.group'),
            ]);
        }

        FilamentAlerts::register(
            NotificationDriver::make('slack')
                ->label('Slack')
                ->driver(SlackDriver::class)
        );
    }

    public static function make(): self
    {
        return new FilamentSlackDriverPlugin;
    }
}
