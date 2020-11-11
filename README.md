# Pusher Beams push notifications channel for Laravel 5.5+, 6.x, 7.x & 8.x

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/pusher-push-notifications.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/pusher-push-notifications)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/pusher-push-notifications/master.svg?style=flat-square)](https://travis-ci.org/laravel-notification-channels/pusher-push-notifications)
[![StyleCI](https://styleci.io/repos/65379321/shield)](https://styleci.io/repos/65379321)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/9015691f-130d-4fca-8710-72a010abc684.svg?style=flat-square)](https://insight.sensiolabs.com/projects/9015691f-130d-4fca-8710-72a010abc684)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/pusher-push-notifications.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/pusher-push-notifications)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/pusher-push-notifications/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/pusher-push-notifications/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/pusher-push-notifications.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/pusher-push-notifications)

This package makes it easy to send [Pusher Beams push notifications](https://pusher.com/docs/beams) with Laravel.

Please note that this notification channel should not be confused with Pusher Channels.

Also please note that prior to version 2.0, this package integrated with Pusher's beta push notifications service that was part of Pusher Channels. Please see Pusher's [migration guide](https://www.pusher.com/docs/channels/push_notifications/migration-guide) for more information.

## Contents

- [Installation](#installation)
	- [Setting up your Pusher account](#setting-up-your-pusher-account)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

You can install the package via composer:

``` bash
composer require laravel-notification-channels/pusher-push-notifications
```

### Setting up your Pusher account

Before using this package you should set up a Pusher Beams account. Here are the steps required.

- Login to https://dash.pusher.com/
- Select the "Beams" product.
- Select your instance from the list or create a new instance.
- Click on the "Settings" tab.
- Upload your APNS Certificate and/or add your FCM Server key.
- Now select the "Credentials" tab.
- Copy your `Instance Id`, and `Secret Key`.
- Add a new entry to in your `config/services.php` file:
  ```php
  'pusher' => [
      'beams_instance_id' => 'Your Instance Id',
      'beams_secret_key' => 'Your Secret Key',
  ],
  ```
- You're now good to go.

## Usage

Now you can use the channel in your `via()` method inside the Notification class.

``` php
use NotificationChannels\PusherPushNotifications\PusherChannel;
use NotificationChannels\PusherPushNotifications\PusherMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [PusherChannel::class];
    }

    public function toPushNotification($notifiable)
    {
        return PusherMessage::create()
            ->iOS()
            ->badge(1)
            ->sound('success')
            ->body("Your {$notifiable->service} account was approved!");
    }
}
```

### Available Message methods

- `platform('')`: Accepts a string value of `iOS`, `Android` or `web`.
- `iOS()`: Sets the platform value to iOS.
- `android()`: Sets the platform value to Android.
- `web()`: Sets the platform value to web.
- `link()`: Accepts a string value which will lead to URI specified on notification click.
- `title('')`: Accepts a string value for the title.
- `body('')`: Accepts a string value for the body.
- `sound('')`: Accepts a string value for the notification sound file. Notice that if you leave blank the default sound value will be `default`.
- `icon('')`: Accepts a string value for the icon file. (Android Only)
- `badge(1)`: Accepts an integer value for the badge. (iOS Only)
- `setOption($key, $value)`: Allows you to set any value in the message payload. See the [request body section of the Pusher Beam docs](https://pusher.com/docs/beams/reference/publish-api#request-body) for more information.

### Sending to multiple platforms

You can send a single message to an iOS device and an Android device at the same time using the `withiOS()` and `withAndroid()` method:

```php
public function toPushNotification($notifiable)
{
    $message = "Your {$notifiable->service} account was approved!";

    return PusherMessage::create()
        ->iOS()
        ->badge(1)
        ->body($message)
        ->withAndroid(
            PusherMessage::create()
                ->title($message)
                ->icon('icon')
        );
}
```

> - Notice that iOS is the default platform, which means you don't have to call `->iOS()`.
> - When using `withAndroid()`, `withiOS()` or `withWeb()` you don't have to define the platform, it's done behind the scenes for you.

### Routing a message

By default, the pusher "interest" messages will be sent to will be defined using the {notifiable}.{id} convention, for example `App.User.1`, 
however you can change this behaviour by including a `routeNotificationFor()` in the notifiable class.

I.e. if you are pushing notification on ``User`` model, you can go to `App\User` class and implement method:

```
public function routeNotificationFor($channel)
{
    if($channel === 'PusherPushNotifications'){
        return 'your.custom.interest.string';
    }

    $class = str_replace('\\', '.', get_class($this));

    return $class.'.'.$this->getKey();
}
```
     
     
     PusherPushNotifications()` in the notifiable class method that 
returns the interest name.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email themsaid@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Mohamed Said](https://github.com/themsaid)
- [Marcel Pociot](https://github.com/mpociot)
- [Freek Van der Herten](https://github.com/freekmurze)
- [Sebastian De Deyne](https://github.com/sebastiandedeyne)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
