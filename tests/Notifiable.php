<?php

namespace NotificationChannels\PusherPushNotifications\Test;

class Notifiable
{
    use \Illuminate\Notifications\Notifiable;

    /**
     * @return int
     */
    public function getKey()
    {
        return 1;
    }
}
