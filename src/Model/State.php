<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;
use JsonSerializable;

final class State implements JsonSerializable
{
    public DateTime $lastDate;
    /** @var StateItem[] */
    public array $items = [];

    private function __construct()
    {
    }

    /** @param array<mixed> $data */
    public static function createFromArray(array $data): self
    {
        $self = new self();

        $self->lastDate = isset($data['lastDate'])
            ? new DateTime($data['lastDate'])
            : new DateTime();

        $self->items = array_map(
            static function (array $item): StateItem {
                return StateItem::createFromArray($item);
            },
            $data['items'] ?? []
        );

        return $self;
    }

    /** @return array<mixed> */
    public function jsonSerialize(): array
    {
        return [
            'lastDate' => $this->lastDate->format('Y-m-d H:i:s'),
            'items' => $this->items,
        ];
    }
}
