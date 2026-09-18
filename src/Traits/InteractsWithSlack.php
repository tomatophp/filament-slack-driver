<?php

namespace TomatoPHP\FilamentSlackDriver\Traits;

use TomatoPHP\FilamentSlackDriver\Jobs\NotifySlackJob;

trait InteractsWithSlack
{
    public function notifySlack(
        string $title,
        ?string $message = null,
        ?string $url = null,
        ?string $image = null,
        ?string $channel = null
    ): void {
        dispatch(new NotifySlackJob([
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'image' => $image,
            'channel' => $channel,
        ]));
    }
}
