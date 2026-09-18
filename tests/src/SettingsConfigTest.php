<?php

namespace TomatoPHP\FilamentSlackDriver\Tests;

use Illuminate\Support\Facades\DB;
use TomatoPHP\FilamentSlackDriver\FilamentSlackDriverServiceProvider;

function saveSlackSetting(string $name, mixed $value): void
{
    DB::table('settings')->updateOrInsert(
        ['group' => 'slack', 'name' => $name],
        ['payload' => json_encode($value), 'locked' => false],
    );
}

function bootSlackProvider(): void
{
    (new FilamentSlackDriverServiceProvider(app()))->boot();
}

it('loads the webhook saved from the settings hub', function () {
    saveSlackSetting('slack_webhook', 'https://hooks.slack.test/services/T111/B111/from-settings');

    bootSlackProvider();

    expect(config('filament-slack-driver.webhook'))->toBe('https://hooks.slack.test/services/T111/B111/from-settings');
});

it('loads the bot token and the default channel saved from the settings hub', function () {
    saveSlackSetting('slack_token', 'xoxb-from-settings');
    saveSlackSetting('slack_channel', '#alerts');

    bootSlackProvider();

    expect(config('filament-slack-driver.token'))->toBe('xoxb-from-settings')
        ->and(config('filament-slack-driver.channel'))->toBe('#alerts');
});

it('keeps the env webhook when the setting is empty', function () {
    config()->set('filament-slack-driver.webhook', 'https://hooks.slack.test/services/T111/B111/from-env');
    saveSlackSetting('slack_webhook', '');

    bootSlackProvider();

    expect(config('filament-slack-driver.webhook'))->toBe('https://hooks.slack.test/services/T111/B111/from-env');
});

it('turns the driver off from the settings hub', function () {
    saveSlackSetting('slack_active', false);

    bootSlackProvider();

    expect(config('filament-slack-driver.active'))->toBeFalse();
});
