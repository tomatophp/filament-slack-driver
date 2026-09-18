<?php

return [
    'settings' => [
        'slack' => [
            'title' => 'Slack Integration',
            'description' => 'Configure your Slack settings.',
            'webhook' => 'Slack Incoming Webhook',
            'token' => 'Slack Bot Token',
            'channel' => 'Default Channel',
            'channel_help' => 'Used with the bot token, for example #general.',
            'active' => 'Slack Notifications Active',
            'keep_secret' => 'Leave it empty to keep the saved value.',
        ],
    ],
    'message' => [
        'view' => 'View',
    ],
];
