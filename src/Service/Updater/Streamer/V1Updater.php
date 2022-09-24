<?php

declare(strict_types=1);

namespace App\Service\Updater\Streamer;

final class V1Updater implements StreamUpdateInterface
{
    public function check(array $content): bool
    {
        if (!isset($content['version'])) {
            return false;
        }

        return (int) $content['version'] >= 1;
    }

    public function update(array $content): array
    {
        return [
            'version' => 1,
            'items' => $content,
        ];
    }
}
