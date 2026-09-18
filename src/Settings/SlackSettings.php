<?php

namespace TomatoPHP\FilamentSlackDriver\Settings;

use Spatie\LaravelSettings\Settings;

class SlackSettings extends Settings
{
    public ?string $slack_webhook = null;

    public ?string $slack_token = null;

    public ?string $slack_channel = null;

    public ?bool $slack_active = true;

    public static function group(): string
    {
        return 'slack';
    }
}
