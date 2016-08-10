<?php

namespace NotificationChannels\PusherPushNotifications;

use Illuminate\Support\Arr;

class Message
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
     * The number to display next to the app icon (iOS).
     *
     * @var int
     */
    protected $badge;

    /**
     * Options.
     *
     * @var array
     */
    protected $options = [];

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
     * @param string $value
     *
     * @return $this
     */
    public function platform($value)
    {
        if (! in_array($value, ['iOS', 'Android'])) {
            throw new \InvalidArgumentException('Invalid platform provided.');
        }

        $this->platform = $value;

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
     * @param string $value
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

        foreach ($this->options as $option => $value) {
            Arr::set($message, $option, $value);
        }

        return $message;
    }

    /**
     * Format the message for Android.
     *
     * @return array
     */
    public function toAndroid()
    {
        $message = [
            'gcm' => [
                'notification' => [
                    'title' => $this->title,
                    'body' => $this->body,
                    'sound' => $this->sound,
                    'icon' => $this->icon,
                ],
            ],
        ];

        foreach ($this->options as $option => $value) {
            Arr::set($message, $option, $value);
        }

        return $message;
    }
}
