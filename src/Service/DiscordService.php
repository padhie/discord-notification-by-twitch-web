<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\StreamerItem;
use GuzzleHttp\Client;
use Padhie\TwitchApiBundle\Response\Streams\Stream;

final class DiscordService
{
    private const PATTERN = '{"content":"%s","embeds":[{"title":"%s","url":"https://twitch.tv/%s","image":{"url":"%s"}}]}';
    private const PLACEHOLDER_TITLE = '%TITLE%';
    private const PLACEHOLDER_NAME = '%NAME%';
    private const PLACEHOLDER_GAME = '%GAME%';

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function executeForStream(StreamerItem $streamer, Stream $twitchStream): void
    {
        $this->sendRequest(
            $streamer->webHook,
            $this->createContent($streamer, $twitchStream)
        );
    }

    private function createContent(StreamerItem $streamer, Stream $twitchStream): string
    {
        $content = str_replace(
            [
                self::PLACEHOLDER_TITLE,
                self::PLACEHOLDER_NAME,
                self::PLACEHOLDER_GAME,
            ],
            [
                $twitchStream->getTitle(),
                $twitchStream->getUserLogin(),
                $twitchStream->getGameName(),
            ],
            $streamer->message
        );

        $thumbnailUrl = $twitchStream->getThumbnailUrl();
        $thumbnailUrl = str_replace(['{width}', '{height}'], [600, 400], $thumbnailUrl);

        return sprintf(
            self::PATTERN,
            $content,
            $twitchStream->getTitle(),
            $twitchStream->getUserLogin(),
            $thumbnailUrl
        );
    }

    private function sendRequest(string $url, string $content): void
    {
        $content = str_replace(["\r", "\n"], '', $content);
        $content = trim($content);

        $this->client->post(
            $url,
            [
                'body' => $content,
                'headers' => ['Content-Type' => 'application/json'],
            ]
        );
    }
}
