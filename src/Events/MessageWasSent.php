<?php

namespace NotificationChannels\PusherPushNotifications\Events;

use Illuminate\Notifications\Notification;

class MessageWasSent
{
    /**
     * @var
     */
    protected $notifiable;

    /**
     * @var \Illuminate\Notifications\Notification
     */
    protected $notification;

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
