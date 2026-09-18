<?php

namespace TomatoPHP\FilamentSlackDriver\Services;

use Filament\Notifications\Notification;
use TomatoPHP\FilamentAlerts\Services\Drivers\Driver;
use TomatoPHP\FilamentSlackDriver\Jobs\NotifySlackJob;

class SlackDriver extends Driver
{
    public function setup(): void
    {
        // TODO: Implement setup() method.
    }

    public function sendIt(
        string $title,
        string $model,
        int | string | null $modelId = null,
        ?string $body = null,
        ?string $url = null,
        ?string $icon = null,
        ?string $image = null,
        ?string $type = 'info',
        ?string $action = 'system',
        ?array $data = [],
        ?int $template_id = null,
        ?Notification $notification = null
    ): void {
        dispatch(new NotifySlackJob([
            'title' => $title,
            'message' => $body,
            'url' => $url,
            'image' => $image ?: $icon,
            'channel' => $data['channel'] ?? null,
        ]))->onQueue(config('filament-alerts.queue'));
    }
}
