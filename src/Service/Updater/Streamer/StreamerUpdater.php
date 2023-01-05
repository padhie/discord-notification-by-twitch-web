<?php

declare(strict_types=1);

namespace App\Service\Updater\Streamer;

use App\Service\StreamerService;

final class StreamerUpdater
{
    public function __construct(
        private readonly StreamerService $streamerService,
        private readonly StreamerUpdaterCollection $streamerUpdaterCollection
    ) {
    }

    public function update(): void
    {
        $rawStreamerContent = $this->streamerService->getRawStreamerContent();
        $json = json_decode($rawStreamerContent, true, 512, JSON_THROW_ON_ERROR);

        $newJson = $this->streamerUpdaterCollection->update($json);

        $newStreamerContent = json_encode($newJson, JSON_THROW_ON_ERROR);
        $this->streamerService->saveRawStreamerContent($newStreamerContent);
    }
}
