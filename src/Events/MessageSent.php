<?php

namespace NotificationChannels\PusherPushNotifications\Events;

use Illuminate\Notifications\Notification;

class MessageSent
{
    /**
     * @var
     */
    private $notifiable;

    /**
     * @var \Illuminate\Notifications\Notification
     */
    private $notification;

    /**
     * MessageSending constructor.
     *
     * @param $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     */
    public function __construct($notifiable, Notification $notification)
    {
        $this->notifiable = $notifiable;
        $this->notification = $notification;
    }
}
