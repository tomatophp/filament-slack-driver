![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-slack-driver/master/art/3x1io-tomato-slack-driver.jpg)

# Filament slack driver

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-slack-driver/version.svg)](https://packagist.org/packages/tomatophp/filament-slack-driver)
[![License](https://poser.pugx.org/tomatophp/filament-slack-driver/license.svg)](https://packagist.org/packages/tomatophp/filament-slack-driver)
[![Downloads](https://poser.pugx.org/tomatophp/filament-slack-driver/d/total.svg)](https://packagist.org/packages/tomatophp/filament-slack-driver)

Slack Channel WebHook Integration For Filament Alerts Sender

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


## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="filament-slack-driver-config"
```

you can publish views file by use this command

```bash
php artisan vendor:publish --tag="filament-slack-driver-views"
```

you can publish languages file by use this command

```bash
php artisan vendor:publish --tag="filament-slack-driver-lang"
```

you can publish migrations file by use this command

```bash
php artisan vendor:publish --tag="filament-slack-driver-migrations"
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Fady Mondy](mailto:info@3x1.io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
