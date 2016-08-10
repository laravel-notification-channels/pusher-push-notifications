<?php

namespace NotificationChannels\PusherPushNotifications\Exceptions;

class CouldNotSendNotification extends \Exception
{
    public static function pusherRespondedWithAnError(array $response)
    {
        /* TODO: not sure that the message is the key message, will have to figure that out */

        return new static("Notification was not sent. Pusher responded with `{$response['code']}: {$response['message']}`");
    }
}
