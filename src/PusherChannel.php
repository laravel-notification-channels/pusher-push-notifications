<?php

namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Pusher\PushNotifications\PushNotifications;
use Throwable;

class PusherChannel
{
    const INTERESTS = 'interests';
    
    /**
     * @var PushNotifications
     */
    protected $beamsClient;

    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    private $events;

    /**
     * @param PushNotifications $beamsClient
     * @param Dispatcher $events
     */
    public function __construct(PushNotifications $beamsClient, Dispatcher $events)
    {
        $this->beamsClient = $beamsClient;
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $type = $notifiable->pushNotificationType ?? self::INTERESTS;

        $data = $notifiable->routeNotificationFor('PusherPushNotifications')
            ?: $this->defaultName($notifiable);

        try {
            $notificationType = sprintf('publishTo%s', Str::ucfirst($type));

            $this->beamsClient->{$notificationType}(
                Arr::wrap($data),
                $notification->toPushNotification($notifiable)->toArray()
            );
        } catch (Throwable $exception) {
            $this->events->dispatch(
                new NotificationFailed($notifiable, $notification, 'pusher-push-notifications')
            );
        }
    }

    /**
     * Get the default name for the notifiable.
     *
     * @param $notifiable
     *
     * @return string
     */
    protected function defaultName($notifiable)
    {
        $class = str_replace('\\', '.', get_class($notifiable));

        return $class.'.'.$notifiable->getKey();
    }
}
