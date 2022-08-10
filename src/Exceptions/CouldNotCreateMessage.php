<?php

namespace NotificationChannels\PusherPushNotifications\Exceptions;

use Exception;

class CouldNotCreateMessage extends Exception
{
    /**
     * @param string $platform
     * @return static
     */
    public static function invalidPlatformGiven(string $platform): static
    {
        return new static("Platform `{$platform}` is invalid. It should be either `iOS` or `Android`.");
    }

    /**
     * @param string $platform
     * @return static
     */
    public static function platformConflict(string $platform): static
    {
        return new static("You are trying to send an extra message to `{$platform}` while the original message is to `{$platform}`.");
    }
}
