<?php

namespace App\Model;

use App\Entity\Notification;
use JsonSerializable;

final class StreamerItem implements JsonSerializable
{
    public string $channel;
    public string $webHook;
    public string $message;

    public function __construct(string $channel, string $webHook, string $message)
    {
        $this->channel = $channel;
        $this->webHook = $webHook;
        $this->message = $message;
    }

    /**
     * @param array<mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['channel'] ?? '',
            $data['webHook'] ?? '',
            $data['message'] ?? ''
        );
    }

    public static function createFromEntity(Notification $notification): self
    {
        return new self(
            $notification->getChannel(),
            $notification->getWebHook(),
            $notification->getMessage()
        );
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'channel' => $this->channel,
            'webHook' => $this->webHook,
            'message' => $this->message,
        ];
    }
}
