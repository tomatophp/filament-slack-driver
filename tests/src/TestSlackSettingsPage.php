<?php

namespace TomatoPHP\FilamentSlackDriver\Tests;

use TomatoPHP\FilamentSlackDriver\Filament\Pages\SlackSettingsPage;
use TomatoPHP\FilamentSlackDriver\Settings\SlackSettings;
use TomatoPHP\FilamentSlackDriver\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('can render Slack Settings Page', function () {
    get(SlackSettingsPage::getUrl())->assertSuccessful();
});

it('saves the Slack settings', function () {
    livewire(SlackSettingsPage::class)
        ->fillForm([
            'slack_webhook' => 'https://hooks.slack.test/services/T222/B222/saved',
            'slack_token' => 'xoxb-saved-token',
            'slack_channel' => '#general',
            'slack_active' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(SlackSettings::class);

    expect($settings->slack_webhook)->toBe('https://hooks.slack.test/services/T222/B222/saved')
        ->and($settings->slack_token)->toBe('xoxb-saved-token')
        ->and($settings->slack_channel)->toBe('#general')
        ->and($settings->slack_active)->toBeTrue();
});

it('keeps the stored secrets when the password inputs are left blank', function () {
    $settings = app(SlackSettings::class);
    $settings->slack_webhook = 'https://hooks.slack.test/services/T333/B333/stored';
    $settings->slack_token = 'xoxb-stored-token';
    $settings->save();

    livewire(SlackSettingsPage::class)
        ->fillForm([
            'slack_webhook' => '',
            'slack_token' => '',
            'slack_channel' => '#renamed',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(SlackSettings::class)->refresh();

    expect($settings->slack_webhook)->toBe('https://hooks.slack.test/services/T333/B333/stored')
        ->and($settings->slack_token)->toBe('xoxb-stored-token')
        ->and($settings->slack_channel)->toBe('#renamed');
});

it('never sends the stored secrets to the browser', function () {
    $settings = app(SlackSettings::class);
    $settings->slack_webhook = 'https://hooks.slack.test/services/T444/B444/super-secret';
    $settings->slack_token = 'xoxb-super-secret-token';
    $settings->slack_channel = '#general';
    $settings->save();

    $page = livewire(SlackSettingsPage::class);

    // Neither the Livewire state nor the rendered HTML may carry the saved credentials.
    expect($page->get('data.slack_webhook'))->toBeNull()
        ->and($page->get('data.slack_token'))->toBeNull();

    $page->assertDontSee('super-secret', escape: false)
        ->assertDontSee('xoxb-super-secret-token', escape: false)
        ->assertSee('#general');

    get(SlackSettingsPage::getUrl())
        ->assertDontSee('super-secret', escape: false)
        ->assertDontSee('xoxb-super-secret-token', escape: false);
});
