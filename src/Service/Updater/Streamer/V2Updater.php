<?php

declare(strict_types=1);

namespace App\Service\Updater\Streamer;

final class V2Updater implements StreamUpdateInterface
{
    public function check(array $content): bool
    {
        if (!isset($content['version'])) {
            return false;
        }

        return (int) $content['version'] >= 2;
    }

    public function update(array $content): array
    {
        return [
            'version' => 2,
            'items' => [
                'padhie' => $content['items'],
            ],
        ];
    }
}
