<?php

namespace NotificationChannels\PusherPushNotifications\Exceptions;

class CouldNotSendNotification extends \Exception
{
    public static function pusherRespondedWithAnError(array $response)
    {
        return new static("Notification was not sent. Pusher responded with `{$response['code']}: {$response['body']}`");
    }
}
