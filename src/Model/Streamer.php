<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

final class Streamer implements JsonSerializable
{
    public const CURRENT_VERSION = 1;

    public int $version;
    /** @var array<string, array<int, StreamerItem>> key = username */
    public array $items = [];

    private function __construct()
    {
    }

    /**
     * @param array{version?: int, items?: array<string, list<array{channel?: string, webHook?: string, message?: string}>>} $data
     */
    public static function createFromArray(array $data): self
    {
        $self = new self();

        $self->version = (int) ($data['version'] ?? 1);

        foreach ($data['items'] ?? [] as $user => $items) {
            foreach ($items as $item) {
                $self->items[$user][] = StreamerItem::createFromArray($item);
            }
        }

        return $self;
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'version' => $this->version,
            'items' => $this->items,
        ];
    }
}
