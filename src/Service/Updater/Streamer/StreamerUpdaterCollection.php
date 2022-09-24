<?php

declare(strict_types=1);

namespace App\Service\Updater\Streamer;

final class StreamerUpdaterCollection
{
    /** @var StreamUpdateInterface[] */
    private array $updater;

    /**
     * @param StreamUpdateInterface[] $updaterList
     */
    public function __construct(array $updaterList)
    {
        $this->updater = $updaterList;
    }

    /**
     * @param array<mixed> $content
     *
     * @return array<mixed>
     */
    public function update(array $content): array
    {
        $newContent = $content;

        foreach ($this->updater as $updater) {
            if ($updater->check($newContent)) {
                continue;
            }

            $newContent = $updater->update($newContent);
        }

        return $newContent;
    }
}
