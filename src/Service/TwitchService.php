<?php

declare(strict_types=1);

namespace App\Service;

use App\Builder\TwitchClientBuilder;
use App\Model\TwitchStreamCollection;
use Padhie\TwitchApiBundle\Exception\ApiErrorException;
use Padhie\TwitchApiBundle\Exception\UserNotExistsException;
use Padhie\TwitchApiBundle\Request\Streams\GetStreamsRequest;
use Padhie\TwitchApiBundle\Response\Streams\GetStreamsResponse;
use Padhie\TwitchApiBundle\Response\Streams\Stream;
use Padhie\TwitchApiBundle\TwitchClient;
use Psr\Log\LoggerInterface;

final class TwitchService
{
    private TwitchClient $twitchClient;
    private LoggerInterface $logger;

    public function __construct(TwitchClientBuilder $twitchClientBuilder, LoggerInterface $logger)
    {
        $this->twitchClient = $twitchClientBuilder->build();
        $this->logger = $logger;
    }

    public function getTwitchStream(string $channelName): ?Stream
    {
        $streams = $this->getTwitchStreams([$channelName]);

        return $streams->get($channelName);
    }

    /**
     * @param string[] $channelNames
     */
    public function getTwitchStreams(array $channelNames): TwitchStreamCollection
    {
        $streamCollection = new TwitchStreamCollection();

        try {
            $requests = [];
            foreach ($channelNames as $channelName) {
                $requests[$channelName] = (new GetStreamsRequest())
                    ->withUserLogin($channelName);
            }

            $streamResponses = $this->twitchClient->sendAsync($requests);

            foreach ($streamResponses as $channelName => $streamResponse) {
                if (!$streamResponse instanceof GetStreamsResponse) {
                    $this->logger->warning('Failed StreamResponse', ['user' => $channelName]);
                    continue;
                }

                $streams = $streamResponse->getStreams();
                $stream = reset($streams);

                if (!$stream instanceof Stream) {
                    continue;
                }

                $streamCollection->add($stream);
            }
        } catch (UserNotExistsException | ApiErrorException $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $streamCollection;
    }
}
