<?php

declare(strict_types=1);

namespace App\Builder;

use GuzzleHttp\Client;
use Padhie\TwitchApiBundle\Request\RequestGenerator;
use Padhie\TwitchApiBundle\Response\ResponseGenerator;
use Padhie\TwitchApiBundle\TwitchClient;

final class TwitchClientBuilder
{
    private string $clientId;
    private string $authorization;

    public function __construct(string $clientId, string $authorization)
    {
        $this->clientId = $clientId;
        $this->authorization = $authorization;
    }

    public function build(): TwitchClient
    {
        return new TwitchClient(
            new Client(),
            new RequestGenerator($this->clientId, $this->authorization),
            new ResponseGenerator()
        );
    }
}
