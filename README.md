![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-slack-driver/master/arts/fadymondy-tomato-slack-driver.jpg)

# Filament Slack Driver

[![Dependabot Updates](https://github.com/tomatophp/filament-slack-driver/actions/workflows/dependabot/dependabot-updates/badge.svg)](https://github.com/tomatophp/filament-slack-driver/actions/workflows/dependabot/dependabot-updates)
[![PHP Code Styling](https://github.com/tomatophp/filament-slack-driver/actions/workflows/fix-php-code-styling.yml/badge.svg)](https://github.com/tomatophp/filament-slack-driver/actions/workflows/fix-php-code-styling.yml)
[![Tests](https://github.com/tomatophp/filament-slack-driver/actions/workflows/tests.yml/badge.svg)](https://github.com/tomatophp/filament-slack-driver/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-slack-driver/version.svg)](https://packagist.org/packages/tomatophp/filament-slack-driver)
[![License](https://poser.pugx.org/tomatophp/filament-slack-driver/license.svg)](https://packagist.org/packages/tomatophp/filament-slack-driver)
[![Downloads](https://poser.pugx.org/tomatophp/filament-slack-driver/d/total.svg)](https://packagist.org/packages/tomatophp/filament-slack-driver)

Slack Channel Integration for [Filament Alerts Sender](https://github.com/tomatophp/filament-alerts)

Send your alerts to Slack through an incoming webhook, or through a bot token and `chat.postMessage`. The title, body,
image and action URL are rendered as a Block Kit message.

## Screenshots

| Light | Dark |
|-------|------|
| ![Settings](https://raw.githubusercontent.com/tomatophp/filament-slack-driver/master/arts/settings-light.png) | ![Settings](https://raw.githubusercontent.com/tomatophp/filament-slack-driver/master/arts/settings-dark.png) |
| ![Settings Hub](https://raw.githubusercontent.com/tomatophp/filament-slack-driver/master/arts/settings-hub-light.png) | ![Settings Hub](https://raw.githubusercontent.com/tomatophp/filament-slack-driver/master/arts/settings-hub-dark.png) |
| ![Driver](https://raw.githubusercontent.com/tomatophp/filament-slack-driver/master/arts/drivers-light.png) | ![Driver](https://raw.githubusercontent.com/tomatophp/filament-slack-driver/master/arts/drivers-dark.png) |

## Requirements

| Package version | Filament | Laravel     | PHP  |
|-----------------|----------|-------------|------|
| 5.x             | 5.x      | 12.x, 13.x  | 8.2+ |

## Installation

```bash
composer require tomatophp/filament-slack-driver
```
after install your package please run this command

```bash
php artisan filament-slack-driver:install
```

finally register the plugin on `/app/Providers/Filament/AdminPanelProvider.php`

```php
->plugin(\TomatoPHP\FilamentSlackDriver\FilamentSlackDriverPlugin::make())
```

## Configuration

open the Slack settings page from the settings hub and fill in either an incoming webhook, or a bot token with a
default channel. The webhook and the bot token are write-only, they are never sent back to the browser, and leaving
an input empty keeps the value that is already stored.

you can also set the values from your `.env` file, a saved setting always wins over the env value:

```dotenv
SLACK_WEBHOOK=https://hooks.slack.com/services/T000/B000/xxxx
SLACK_BOT_TOKEN=xoxb-your-bot-token
SLACK_CHANNEL=#general
SLACK_ACTIVE=true
```

when nothing is configured, or when the driver is switched off, nothing is sent and nothing is logged.

## Usage

to set up any model to get notifications you

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TomatoPHP\FilamentSlackDriver\Traits\InteractsWithSlack;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use InteractsWithSlack;
    ...
```

### Queue

the notification is run on queue, so you must run the queue worker to send the notifications

```bash
php artisan queue:work
```

### Use Filament Native Notification

you can use the filament native notification and we add some `macro` for you

```php
use Filament\Notifications\Notification;

Notification::make('send')
    ->title('Test Notifications')
    ->body('This is a test notification')
    ->icon('heroicon-o-bell')
    ->color('success')
    ->actions([
        \Filament\Notifications\Actions\Action::make('view')
            ->label('View')
            ->url('https://google.com')
            ->markAsRead()
    ])
    ->sendUse(auth()->user(), \TomatoPHP\FilamentSlackDriver\Services\SlackDriver::class, ['image' => 'https://via.placeholder.com/150']);

```

### Notification Service

to create a new template you can use template CRUD and make sure that the template key is unique because you will use it on every single notification.

### Send Notification

to send a notification you must use our helper SendNotification::class like

```php
use TomatoPHP\FilamentAlerts\Facades\FilamentAlerts;

FilamentAlerts::notify(User::first())
    ->template($template->id)
    ->title([
        "find-text" => "change with this"
    ])
    ->body([
        "find-text" => "change with this"
    ])
    ->drivers(\TomatoPHP\FilamentSlackDriver\Services\SlackDriver::class)
    ->data(['channel' => '#releases'])
    ->send();
```

where `$template` is selected of the template by key or id, and title, body use to select and replace string on the template with custom data.
the `channel` data key overrides the default channel, it only applies when you send with a bot token.

### Notification Channels

it can be working with direct user methods like

```php
$user->notifySlack(string $title, ?string $message = null, ?string $url = null, ?string $image = null, ?string $channel = null);
```

## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="filament-slack-driver-config"
```

you can publish languages file by use this command

```bash
php artisan vendor:publish --tag="filament-slack-driver-lang"
```

## Testing

if you like to run `PEST` testing just use this command

```bash
composer test
```

## Code Style

if you like to fix the code style just use this command

```bash
composer format
```

## PHPStan

if you like to check the code by `PHPStan` just use this command

```bash
composer analyse
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Fady Mondy](mailto:info@3x1.io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Other Filament Packages

Checkout our [Awesome TomatoPHP](https://github.com/tomatophp/awesome)
