<?php

namespace App\Controller;

use App\Model\StreamerItem;
use App\Repository\NotificationRepository;
use App\Service\DiscordService;
use App\Service\InfluxService;
use App\Service\MessagePreparer;
use App\Service\TwitchService;
use Padhie\TwitchApiBundle\Response\Streams\Stream;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TestController extends AbstractController
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository,
        private readonly TwitchService $twitchService,
        private readonly DiscordService $discordService,
        private readonly InfluxService $influxService,
        private readonly MessagePreparer $messagePreparer
    ) {
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(Request $request): Response
    {
        $id = $request->get('id');

        if ($id === null) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'no channel given',
            ]);
        }

        $notification = $this->notificationRepository->find((int) $id);
        if ($notification === null) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Channel not configure',
            ]);
        }

        $stream = StreamerItem::createFromEntity($notification);
        $channel = $stream->channel;

        $twitchStream = $this->twitchService->getTwitchStream($channel);
        if ($twitchStream === null) {
            $twitchStream = Stream::createFromArray([
                'id' => '0',
                'user_id' => '0',
                'user_login' => $channel,
                'game_id' => '',
                'game_name' => '',
                'type' => '',
                'title' => 'offline',
                'viewer_count' => 0,
                'started_at' => (new \DateTimeImmutable())->format('Y-m-d\TH:i:s\Z'),
                'language' => '',
                'thumbnail_url' => '',
                'tag_ids' => [],
                'is_mature' => false,
            ]);
        }

        $stream->message = $this->messagePreparer->prepareForTesting($stream->message);
        $this->discordService->executeForStream($stream, $twitchStream);
        $this->influxService->push($channel, 1);

        return new JsonResponse([
            'status' => 'success',
        ]);
    }
}
