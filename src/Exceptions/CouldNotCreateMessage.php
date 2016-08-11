<?php

namespace NotificationChannels\PusherPushNotifications\Exceptions;

use Exception;

class CouldNotCreateMessage extends Exception
{
    /**
     * @param string $platform
     *
     * @return static
     */
    public static function invalidPlatformGiven($platform)
    {
        return new static("Platform `{$platform}` is invalid. It should be either `iOS` or `Android`.");
    }
}
