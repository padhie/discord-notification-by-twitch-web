<?php

namespace App\Service;

use App\Model\InfluxConfig;
use App\Model\Output;
use App\Model\StreamerItem;
use App\Model\TwitchConfig;
use App\Repository\NotificationRepository;

final class OutputHelper
{
    public function __construct(
        private readonly TwitchConfig $twitchConfig,
        private readonly InfluxConfig $influxConfig,
        private readonly NotificationRepository $notificationRepository
    ) {
    }

    public function generateOutput(): Output
    {
        return new Output(
            $this->twitchConfig,
            $this->influxConfig,
            $this->getStreamerItems(),
        );
    }

    /**
     * @return StreamerItem[]
     */
    private function getStreamerItems(): array
    {
        $notifications = $this->notificationRepository->findAll();

        $outputItems = [];
        foreach ($notifications as $notification) {
            $outputItems[] = StreamerItem::createFromEntity($notification);
        }

        return $outputItems;
    }
}
