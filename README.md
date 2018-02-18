# Pusher push notifications channel for Laravel 5.6

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/pusher-push-notifications.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/pusher-push-notifications)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/pusher-push-notifications/master.svg?style=flat-square)](https://travis-ci.org/laravel-notification-channels/pusher-push-notifications)
[![StyleCI](https://styleci.io/repos/65379321/shield)](https://styleci.io/repos/65379321)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/9015691f-130d-4fca-8710-72a010abc684.svg?style=flat-square)](https://insight.sensiolabs.com/projects/9015691f-130d-4fca-8710-72a010abc684)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/pusher-push-notifications.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/pusher-push-notifications)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/pusher-push-notifications/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/pusher-push-notifications/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/pusher-push-notifications.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/pusher-push-notifications)

This package makes it easy to send [Pusher push notifications](https://docs.pusher.com/push-notifications) with Laravel 5.5 and 5.6. If you are using Laravel 5.3 or 5.4, use 1.x branch. Since pusher dropped the support of API key and secret for push notification on new projects created on https://dashboard.pusher.com in favor of new https://dash.pusher.com, this package only support the latest version of pusher push notifications.

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

Before using this package you should set up a Pusher account. Here are the steps required.

- Login to https://dash.pusher.com/
- Add a new instance in Push Notifications service.
- Upload your APNS Certificate or add your GCM API key.
- Now select your instance in left navigation area.
- Copy your `instance_id`, and `secret`.
- Update the values in your `config/broadcasting.php` file under the pusher connection with name `push_notification_instance_id` and `push_notification_secret`
- You're now good to go.

### Config

In `config/broadcasting.php`, it should looks like this

``` php
    'connections' => [
        'pusher' => [
            'push_notification_instance_id' => 'YOUR INSTANCE ID HERE', // Looks like a uuid
            'push_notification_secret' => "YOUR SECRET HERE", // 31 char string
        ],
    ],
```

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

- `platform('')`: Accepts a string value of `iOS` or `Android`.
- `iOS()`: Sets the platform value to iOS.
- `android()`: Sets the platform value to Android.
- `title('')`: Accepts a string value for the title.
- `body('')`: Accepts a string value for the body.
- `sound('')`: Accepts a string value for the notification sound file. Notice that if you leave blank the default sound value will be `default`.
- `icon('')`: Accepts a string value for the icon file. (Android Only)
- `badge(1)`: Accepts an integer value for the badge. (iOS Only)
- `setOption($key, $value)`: Allows you to set any value in the message payload. For more information [check here for iOS](https://pusher.com/docs/push_notifications/ios/server), [or here for Android](https://pusher.com/docs/push_notifications/android/server).

### Sending to multiple platforms

You can send a single message to an iOS device and an Android device at the same time using the `withiOS()` and `withAndroid()` method:

``` php
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
> - When using `withAndroid()` or `withiOS()` you don't have to define the platform, it's done behind the scenes for you.

### Routing a message

By default the pusher "interest" messages will be sent to will be defined using the {notifiable}.{id} convention, for example `App.User.1`, however you can change this behaviour by including a `routeNotificationForPusherPushNotifications()` in the notifiable class method that returns the interest name.

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
- [Hanlin Wang](https://github.com/wanghanlin)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
