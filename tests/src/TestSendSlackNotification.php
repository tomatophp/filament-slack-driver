<?php

namespace TomatoPHP\FilamentSlackDriver\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use TomatoPHP\FilamentAlerts\Facades\FilamentAlerts;
use TomatoPHP\FilamentSlackDriver\Jobs\NotifySlackJob;
use TomatoPHP\FilamentSlackDriver\Services\SlackDriver;
use TomatoPHP\FilamentSlackDriver\Tests\Models\NotificationsTemplate;
use TomatoPHP\FilamentSlackDriver\Tests\Models\User;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('can use FilamentAlerts Facade To Notify Slack Channel', function () {
    $user = User::factory()->create();
    $template = NotificationsTemplate::factory()->create();

    FilamentAlerts::notify($user)
        ->template($template->id)
        ->drivers([SlackDriver::class])
        ->title([
            'name' => $user->name,
        ])
        ->body([
            'date' => now()->toDateTimeString(),
        ])
        ->send();

    assertDatabaseHas('notifications_logs', [
        'title' => $template->title,
        'description' => $template->body,
        'provider' => 'slack',
        'type' => 'info',
    ]);
});

it('posts a Block Kit message to the incoming webhook', function () {
    dispatch(new NotifySlackJob([
        'title' => 'Server is down',
        'message' => 'The queue worker stopped responding',
        'url' => 'https://tomatophp.com/status',
        'image' => 'https://tomatophp.com/logo.png',
    ]));

    Http::assertSent(function (Request $request): bool {
        expect($request->url())->toBe('https://hooks.slack.test/services/T000/B000/xxx');

        $body = $request->data();

        expect($body['text'])->toBe('Server is down')
            ->and($body['blocks'][0]['type'])->toBe('header')
            ->and($body['blocks'][0]['text']['text'])->toBe('Server is down')
            ->and($body['blocks'][1]['type'])->toBe('section')
            ->and($body['blocks'][1]['text']['text'])->toBe('The queue worker stopped responding')
            ->and($body['blocks'][1]['accessory']['image_url'])->toBe('https://tomatophp.com/logo.png')
            ->and($body['blocks'][2]['type'])->toBe('actions')
            ->and($body['blocks'][2]['elements'][0]['url'])->toBe('https://tomatophp.com/status');

        return true;
    });
});

it('posts through chat.postMessage when only a bot token is configured', function () {
    config()->set('filament-slack-driver.webhook', null);
    config()->set('filament-slack-driver.token', 'xoxb-test-token');
    config()->set('filament-slack-driver.channel', '#alerts');

    dispatch(new NotifySlackJob([
        'title' => 'Deployed',
        'message' => 'Version 5.0.0 is live',
    ]));

    Http::assertSent(function (Request $request): bool {
        expect($request->url())->toBe('https://slack.com/api/chat.postMessage')
            ->and($request->hasHeader('Authorization', 'Bearer xoxb-test-token'))->toBeTrue()
            ->and($request->data()['channel'])->toBe('#alerts');

        return true;
    });
});

it('sends to the channel given on the job over the default channel', function () {
    config()->set('filament-slack-driver.webhook', null);
    config()->set('filament-slack-driver.token', 'xoxb-test-token');
    config()->set('filament-slack-driver.channel', '#alerts');

    dispatch(new NotifySlackJob([
        'title' => 'Deployed',
        'message' => 'Version 5.0.0 is live',
        'channel' => '#releases',
    ]));

    Http::assertSent(fn (Request $request): bool => $request->data()['channel'] === '#releases');
});

it('skips sending when Slack is not configured', function () {
    config()->set('filament-slack-driver.webhook', null);
    config()->set('filament-slack-driver.token', null);
    config()->set('filament-slack-driver.channel', null);

    dispatch(new NotifySlackJob([
        'title' => 'Server is down',
        'message' => 'Nobody should hear about this',
    ]));

    Http::assertNothingSent();
    assertDatabaseCount('notifications_logs', 0);
});

it('skips sending when the driver is turned off', function () {
    config()->set('filament-slack-driver.active', false);

    dispatch(new NotifySlackJob([
        'title' => 'Server is down',
        'message' => 'Nobody should hear about this',
    ]));

    Http::assertNothingSent();
    assertDatabaseCount('notifications_logs', 0);
});

it('skips sending when only a bot token without a channel is configured', function () {
    config()->set('filament-slack-driver.webhook', null);
    config()->set('filament-slack-driver.token', 'xoxb-test-token');
    config()->set('filament-slack-driver.channel', null);

    dispatch(new NotifySlackJob([
        'title' => 'Server is down',
    ]));

    Http::assertNothingSent();
});

it('sends the image on its own block when there is no body', function () {
    dispatch(new NotifySlackJob([
        'title' => 'Look at this',
        'image' => 'https://tomatophp.com/logo.png',
    ]));

    Http::assertSent(function (Request $request): bool {
        $blocks = $request->data()['blocks'];

        expect($blocks[1]['type'])->toBe('image')
            ->and($blocks[1]['image_url'])->toBe('https://tomatophp.com/logo.png');

        return true;
    });
});

it('can notify a model through the InteractsWithSlack trait', function () {
    User::factory()->create()->notifySlack('Hello', 'From the trait');

    Http::assertSent(fn (Request $request): bool => $request->data()['text'] === 'Hello');
});
