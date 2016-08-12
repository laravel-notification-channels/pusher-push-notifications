<?php

namespace NotificationChannels\PusherPushNotifications\Events;

use Illuminate\Notifications\Notification;

class MessageWasSent
{
    /** @var mixed */
    protected $notifiable;

    /** @var \Illuminate\Notifications\Notification */
    protected $notification;

    /**
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     */
    public function __construct($notifiable, Notification $notification)
    {
        $this->notifiable = $notifiable;

        $this->notification = $notification;
    }
}
