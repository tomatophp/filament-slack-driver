<?php

return [
    /**
     * ---------------------------------------
     * Slack Incoming WebHook URL
     * ---------------------------------------
     */
    'webhook' => env('SLACK_WEBHOOK'),

    /**
     * ---------------------------------------
     * Slack Bot Token used by chat.postMessage
     * ---------------------------------------
     */
    'token' => env('SLACK_BOT_TOKEN'),

    /**
     * ---------------------------------------
     * Default Channel used with the Bot Token
     * ---------------------------------------
     */
    'channel' => env('SLACK_CHANNEL'),

    /**
     * ---------------------------------------
     * Allow Sending Slack Notifications
     * ---------------------------------------
     */
    'active' => env('SLACK_ACTIVE', true),

    /**
     * ---------------------------------------
     * Slack Web API Endpoint
     * ---------------------------------------
     */
    'endpoint' => env('SLACK_ENDPOINT', 'https://slack.com/api/chat.postMessage'),
];
