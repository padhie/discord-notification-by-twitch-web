<?php

namespace App\Service;

use App\Model\InfluxConfig;
use App\Model\Output;
use App\Model\StreamerItem;
use App\Model\TwitchConfig;
use App\Repository\NotificationRepository;

final class OutputHelper
{
    private TwitchConfig $twitchConfig;
    private InfluxConfig $influxConfig;
    private NotificationRepository $notificationRepository;

    public function __construct(TwitchConfig $twitchConfig, InfluxConfig $influxConfig, NotificationRepository $notificationRepository)
    {
        $this->twitchConfig = $twitchConfig;
        $this->influxConfig = $influxConfig;
        $this->notificationRepository = $notificationRepository;
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
