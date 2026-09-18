<?php

namespace TomatoPHP\FilamentSlackDriver\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TomatoPHP\FilamentAlerts\Models\NotificationsLogs;
use TomatoPHP\FilamentSlackDriver\Services\Slack;

class NotifySlackJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ?string $title;

    public ?string $message;

    public ?string $url;

    public ?string $image;

    public ?string $channel;

    /**
     * Create a new notification instance.
     *
     * @param  array{title: ?string, message?: ?string, url?: ?string, image?: ?string, channel?: ?string}  $arg
     */
    public function __construct(array $arg)
    {
        $this->title = $arg['title'];
        $this->message = $arg['message'] ?? null;
        $this->url = $arg['url'] ?? null;
        $this->image = $arg['image'] ?? null;
        $this->channel = $arg['channel'] ?? null;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // An unconfigured or disabled Slack integration is skipped quietly, nothing is sent and nothing is logged.
        if (! Slack::isConfigured()) {
            return;
        }

        Slack::send([
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'image' => $this->image,
            'channel' => $this->channel,
        ]);

        $log = new NotificationsLogs;
        $log->title = $this->title;
        $log->description = $this->message;
        $log->provider = 'slack';
        $log->type = 'info';
        $log->save();
    }
}
