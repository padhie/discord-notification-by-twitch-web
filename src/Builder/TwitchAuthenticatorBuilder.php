<?php

declare(strict_types=1);

namespace App\Builder;

use Padhie\TwitchApiBundle\TwitchAuthenticator;

final class TwitchAuthenticatorBuilder
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $redirectUrl,
    ) {
    }

    public function build(): TwitchAuthenticator
    {
        return new TwitchAuthenticator(
            $this->clientId,
            $this->redirectUrl,
        );
    }
}