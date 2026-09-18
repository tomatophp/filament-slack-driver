<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('slack.slack_webhook', '');
        $this->migrator->add('slack.slack_token', '');
        $this->migrator->add('slack.slack_channel', '');
        $this->migrator->add('slack.slack_active', true);
    }
};
