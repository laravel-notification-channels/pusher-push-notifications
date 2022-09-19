<?php

namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Support\Arr;
use NotificationChannels\PusherPushNotifications\Exceptions\CouldNotCreateMessage;

class PusherMessage
{
    /**
     * The device platform (iOS/Android).
     */
    protected string $platform = 'iOS';

    /**
     * The message title.
     */
    protected string|null $title = null;

    /**
     * The phone number the message should be sent from.
     */
    protected string $sound = 'default';

    /**
     * The message icon (Android).
     */
    protected string|null $icon = null;

    /**
     * The number to display next to the push notification (iOS).
     */
    protected int|null $badge = null;

    /**
     * URL to follow on notification click.
     */
    protected string|null $link = null;

    /**
     * Extra options that will get added to the message.
     */
    protected array $options = [];

    /**
     * An extra message to the other platform.
     *
     * @var
     */
    protected PusherMessage|null $extraMessage = null;

    /**
     * @param  string  $body
     */
    public function __construct(protected string $body = '')
    {
    }

    /**
     * @param  string  $body
     * @return static
     */
    public static function create(string $body = ''): static
    {
        return new static($body);
    }

    /**
     * Set the platform [iOS/Android].
     *
     * @param  string  $platform
     * @return $this
     *
     * @throws \NotificationChannels\PusherPushNotifications\Exceptions\CouldNotCreateMessage
     */
    public function platform(string $platform): self
    {
        if (! in_array($platform, ['iOS', 'Android', 'web'])) {
            throw CouldNotCreateMessage::invalidPlatformGiven($platform);
        }

        $this->platform = $platform;

        return $this;
    }

    /**
     * Set the platform to iOS.
     *
     * @return $this
     */
    public function iOS(): self
    {
        $this->platform = 'iOS';

        return $this;
    }

    /**
     * Set the platform to Android.
     *
     * @return $this
     */
    public function android(): self
    {
        $this->platform = 'Android';

        return $this;
    }

    /**
     * Set the platform to web.
     *
     * @return $this
     */
    public function web(): self
    {
        $this->platform = 'web';

        return $this;
    }

    /**
     * Set an extra message to be sent to Android.
     *
     * @param  \NotificationChannels\PusherPushNotifications\PusherMessage  $message
     * @return $this
     *
     * @throws CouldNotCreateMessage
     */
    public function withAndroid(self $message): self
    {
        $this->withExtra($message->android());

        return $this;
    }

    /**
     * Set an extra message to be sent to iOS.
     *
     * @param  \NotificationChannels\PusherPushNotifications\PusherMessage  $message
     * @return $this
     *
     * @throws CouldNotCreateMessage
     */
    public function withiOS(self $message): self
    {
        $this->withExtra($message->iOS());

        return $this;
    }

    /**
     * Set an extra message to be sent to web.
     *
     * @param  \NotificationChannels\PusherPushNotifications\PusherMessage  $message
     * @return $this
     *
     * @throws CouldNotCreateMessage
     */
    public function withWeb(self $message): self
    {
        $this->withExtra($message->web());

        return $this;
    }

    /**
     * Set an extra message to be sent to another platform.
     *
     * @param  \NotificationChannels\PusherPushNotifications\PusherMessage  $message
     * @return void
     *
     * @throws CouldNotCreateMessage
     */
    private function withExtra(self $message): void
    {
        if ($message->getPlatform() === $this->platform) {
            throw CouldNotCreateMessage::platformConflict($this->platform);
        }

        $this->extraMessage = $message;
    }

    /**
     * Set the message title.
     *
     * @param  string  $value
     * @return $this
     */
    public function title(string $value): self
    {
        $this->title = $value;

        return $this;
    }

    /**
     * Set the message body.
     *
     * @param  string  $value
     * @return $this
     */
    public function body(string $value): self
    {
        $this->body = $value;

        return $this;
    }

    /**
     * Set the message sound (Android).
     *
     * @param  string  $value
     * @return $this
     */
    public function sound(string $value): self
    {
        $this->sound = $value;

        return $this;
    }

    /**
     * Set the message icon (Android).
     *
     * @param  string  $value
     * @return $this
     */
    public function icon(string $value): self
    {
        $this->icon = $value;

        return $this;
    }

    /**
     * Set the message badge (iOS).
     *
     * @param  int  $value
     * @return $this
     */
    public function badge(int $value): self
    {
        $this->badge = $value;

        return $this;
    }

    /**
     * Set the message link.
     *
     * @param  string  $value
     * @return $this
     */
    public function link(string $value): self
    {
        $this->link = $value;

        return $this;
    }

    /**
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setOption(string $key, mixed $value): self
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray(): array
    {
        return match ($this->platform) {
            'Android' => $this->toAndroid(),
            'web'     => $this->toWeb(),
            default   => $this->toiOS(),
        };
    }

    /**
     * Format the message for iOS.
     *
     * @return array
     */
    public function toiOS(): array
    {
        $message = [
            'apns' => [
                'aps' => [
                    'alert' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                    'sound' => $this->sound,
                    'badge' => $this->badge,
                ],
            ],
        ];

        $this->formatMessage($message);

        return $message;
    }

    /**
     * Format the message for Android.
     *
     * @return array
     */
    public function toAndroid(): array
    {
        $message = [
            'fcm' => [
                'notification' => array_filter([
                    'title' => $this->title,
                    'body' => $this->body,
                    'sound' => $this->sound,
                    'icon' => $this->icon,
                ]),
            ],
        ];

        $this->formatMessage($message);

        return $message;
    }

    /**
     * Format the message for web.
     *
     * @return array
     */
    public function toWeb(): array
    {
        $message = [
            'web' => [
                'notification' => array_filter([
                    'title' => $this->title,
                    'body' => $this->body,
                    'sound' => $this->sound,
                    'icon' => $this->icon,
                    'deep_link' => $this->link,
                ]),
            ],
        ];

        $this->formatMessage($message);

        return $message;
    }

    /**
     * Return the current platform.
     *
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * Format the final Payload.
     *
     * @param $message
     */
    private function formatMessage(&$message): void
    {
        if ($this->extraMessage) {
            $message = array_merge($message, $this->extraMessage->toArray());
        }

        foreach ($this->options as $option => $value) {
            Arr::set($message, $option, $value);
        }
    }
}
