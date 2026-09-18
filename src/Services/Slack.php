<?php

namespace TomatoPHP\FilamentSlackDriver\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Slack
{
    /**
     * Whether the driver is enabled and at least one transport is configured.
     */
    public static function isConfigured(): bool
    {
        if (! config('filament-slack-driver.active')) {
            return false;
        }

        return filled(config('filament-slack-driver.webhook'))
            || (filled(config('filament-slack-driver.token')) && filled(config('filament-slack-driver.channel')));
    }

    /**
     * Send a message to Slack, preferring the incoming webhook over the bot token.
     *
     * @param  array{title: ?string, message: ?string, url: ?string, image: ?string, channel?: ?string}  $payload
     */
    public static function send(array $payload): ?Response
    {
        if (! static::isConfigured()) {
            return null;
        }

        $body = static::blocks($payload);

        $webhook = config('filament-slack-driver.webhook');

        if (filled($webhook)) {
            return Http::asJson()->post((string) $webhook, $body);
        }

        $body['channel'] = $payload['channel'] ?: config('filament-slack-driver.channel');

        return Http::withToken((string) config('filament-slack-driver.token'))
            ->asJson()
            ->post((string) config('filament-slack-driver.endpoint'), $body);
    }

    /**
     * Build the Block Kit body of the message.
     *
     * @param  array{title: ?string, message: ?string, url: ?string, image: ?string, channel?: ?string}  $payload
     * @return array<string, mixed>
     */
    public static function blocks(array $payload): array
    {
        $title = $payload['title'] ?? null;
        $message = $payload['message'] ?? null;
        $url = $payload['url'] ?? null;
        $image = $payload['image'] ?? null;

        $blocks = [];

        if (filled($title)) {
            $blocks[] = [
                'type' => 'header',
                'text' => [
                    'type' => 'plain_text',
                    'text' => (string) str($title)->limit(150),
                    'emoji' => true,
                ],
            ];
        }

        if (filled($message)) {
            $section = [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => (string) str($message)->limit(2900),
                ],
            ];

            // A small image rides along the body as the section accessory, a large one gets its own block.
            if (filled($image)) {
                $section['accessory'] = [
                    'type' => 'image',
                    'image_url' => $image,
                    'alt_text' => (string) ($title ?? 'image'),
                ];
            }

            $blocks[] = $section;
        } elseif (filled($image)) {
            $blocks[] = [
                'type' => 'image',
                'image_url' => $image,
                'alt_text' => (string) ($title ?? 'image'),
            ];
        }

        if (filled($url)) {
            $blocks[] = [
                'type' => 'actions',
                'elements' => [
                    [
                        'type' => 'button',
                        'style' => 'primary',
                        'url' => $url,
                        'text' => [
                            'type' => 'plain_text',
                            'text' => trans('filament-slack-driver::messages.message.view'),
                            'emoji' => true,
                        ],
                    ],
                ],
            ];
        }

        return [
            // `text` is the notification preview and the fallback for clients that cannot render blocks.
            'text' => (string) ($title ?: $message ?: ''),
            'blocks' => $blocks,
        ];
    }
}
