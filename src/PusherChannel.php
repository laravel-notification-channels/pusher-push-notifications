<?php

declare(strict_types=1);

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
    public const INTERESTS = 'interests';

    public function __construct(protected PushNotifications $beamsClient, private Dispatcher $events)
    {
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification): void
    {
        $type = $notifiable->pushNotificationType ?? self::INTERESTS;

        $data = $notifiable->routeNotificationFor('PusherPushNotifications')
            ?: self::defaultName($notifiable);

        try {
            $notificationType = sprintf('publishTo%s', Str::ucfirst($type));

            $this->beamsClient->{$notificationType}(
                Arr::wrap($data),
                $notification->toPushNotification($notifiable)->toArray()
            );
        } catch (Throwable $exception) {
            $this->events->dispatch(
                new NotificationFailed($notifiable, $notification, 'pusher-push-notifications', ['exception' => $exception])
            );
        }
    }

    /**
     * Get the default name for the notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public static function defaultName(mixed $notifiable): string
    {
        $class = str_replace('\\', '.', get_class($notifiable));

        return $class.'.'.$notifiable->getKey();
    }
}
