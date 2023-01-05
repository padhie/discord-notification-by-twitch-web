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

    public function __construct(
        TwitchClientBuilder $twitchClientBuilder,
        private readonly LoggerInterface $logger
    ) {
        $this->twitchClient = $twitchClientBuilder->build();
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
            $request = new GetStreamsRequest();
            foreach ($channelNames as $channelName) {
                $request = $request->withUserLogin($channelName);
            }

            $streamResponse = $this->twitchClient->send($request);
            if (!$streamResponse instanceof GetStreamsResponse) {
                return $streamCollection;
            }

            foreach ($streamResponse->getStreams() as $stream) {
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
