<?php

declare(strict_types=1);

namespace App\Service\Updater\Streamer;

interface StreamUpdateInterface
{
    /**
     * @param array<mixed> $content
     */
    public function check(array $content): bool;

    /**
     * @param array<mixed> $content
     *
     * @return array<mixed>
     */
    public function update(array $content): array;
}
