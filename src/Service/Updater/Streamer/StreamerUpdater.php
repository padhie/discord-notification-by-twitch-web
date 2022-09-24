<?php

declare(strict_types=1);

namespace App\Service\Updater\Streamer;

use App\Service\StreamerService;

final class StreamerUpdater
{
    private StreamerService $streamerService;
    private StreamerUpdaterCollection $streamerUpdaterCollection;

    public function __construct(StreamerService $streamerService, StreamerUpdaterCollection $streamerUpdaterCollection)
    {
        $this->streamerService = $streamerService;
        $this->streamerUpdaterCollection = $streamerUpdaterCollection;
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
