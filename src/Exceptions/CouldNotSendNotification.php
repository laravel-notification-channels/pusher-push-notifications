<?php

namespace NotificationChannels\PusherPushNotifications\Exceptions;

use Exception;

class CouldNotSendNotification extends Exception
{
    /**
     * @param array $response
     *
     * @return static
     */
    public static function pusherRespondedWithAnError(array $response)
    {
        return new static("Notification was not sent. Pusher responded with `{$response['code']}: {$response['body']}`");
    }
}
