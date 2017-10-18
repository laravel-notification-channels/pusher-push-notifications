<?php

namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Support\Arr;
use NotificationChannels\PusherPushNotifications\Exceptions\CouldNotCreateMessage;

class PusherMessage
{
    /**
     * The device platform (iOS/Android).
     *
     * @var string
     */
    protected $platform = 'iOS';

    /**
     * The message title.
     *
     * @var string
     */
    protected $title;

    /**
     * The message body.
     *
     * @var string
     */
    protected $body;

    /**
     * The phone number the message should be sent from.
     *
     * @var string
     */
    protected $sound = 'default';

    /**
     * The message icon (Android).
     *
     * @var string
     */
    protected $icon;

    /**
     * The number to display next to the push notification (iOS).
     *
     * @var int
     */
    protected $badge;

    /**
     * Extra options that will get added to the message.
     *
     * @var array
     */
    protected $options = [];

    /**
     * An extra message to the other platform.
     *
     * @var
     */
    protected $extraMessage;

    /**
     * @param string $body
     *
     * @return static
     */
    public static function create($body = '')
    {
        return new static($body);
    }

    /**
     * @param string $body
     */
    public function __construct($body = '')
    {
        $this->body = $body;
    }

    /**
     * Set the platform [iOS/Android].
     *
     * @param string $platform
     *
     * @return $this
     *
     * @throws \NotificationChannels\PusherPushNotifications\Exceptions\CouldNotCreateMessage
     */
    public function platform($platform)
    {
        if (! in_array($platform, ['iOS', 'Android'])) {
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
    public function iOS()
    {
        $this->platform = 'iOS';

        return $this;
    }

    /**
     * Set the platform to Android.
     *
     * @return $this
     */
    public function android()
    {
        $this->platform = 'Android';

        return $this;
    }

    /**
     * Set an extra message to be sent to Android.
     *
     * @param \NotificationChannels\PusherPushNotifications\PusherMessage $message
     * @return $this
     */
    public function withAndroid(PusherMessage $message)
    {
        $this->withExtra($message->android());

        return $this;
    }

    /**
     * Set an extra message to be sent to iOS.
     *
     * @param \NotificationChannels\PusherPushNotifications\PusherMessage $message
     * @return $this
     */
    public function withiOS(PusherMessage $message)
    {
        $this->withExtra($message->iOS());

        return $this;
    }

    /**
     * Set an extra message to be sent to another platform.
     *
     * @param \NotificationChannels\PusherPushNotifications\PusherMessage $message
     * @return void
     */
    private function withExtra(PusherMessage $message)
    {
        if ($message->getPlatform() == $this->platform) {
            throw CouldNotCreateMessage::platformConflict($this->platform);
        }

        $this->extraMessage = $message;
    }

    /**
     * Set the message title.
     *
     * @param string $value
     *
     * @return $this
     */
    public function title($value)
    {
        $this->title = $value;

        return $this;
    }

    /**
     * Set the message body.
     *
     * @param string $value
     *
     * @return $this
     */
    public function body($value)
    {
        $this->body = $value;

        return $this;
    }

    /**
     * Set the message sound (Android).
     *
     * @param string $value
     *
     * @return $this
     */
    public function sound($value)
    {
        $this->sound = $value;

        return $this;
    }

    /**
     * Set the message icon (Android).
     *
     * @param string $value
     *
     * @return $this
     */
    public function icon($value)
    {
        $this->icon = $value;

        return $this;
    }

    /**
     * Set the message badge (iOS).
     *
     * @param int $value
     *
     * @return $this
     */
    public function badge($value)
    {
        $this->badge = (int) $value;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->platform === 'iOS'
            ? $this->toiOS()
            : $this->toAndroid();
    }

    /**
     * Format the message for iOS.
     *
     * @return array
     */
    public function toiOS()
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
     * Changed from GCM to FCM
     *
     * @return array
     */
    public function toAndroid()
    {
        $message = [
            'fcm' => [
                'data' => [
                    'title' => $this->title,
                    'body' => $this->body,
                    'sound' => $this->sound,
                    'icon' => $this->icon,
                ],
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
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Format the final Payload.
     *
     * @param $message
     */
    private function formatMessage(&$message)
    {
        if ($this->extraMessage) {
            $message = array_merge($message, $this->extraMessage->toArray());
        }

        foreach ($this->options as $option => $value) {
            Arr::set($message, $option, $value);
        }
    }
}
