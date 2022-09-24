<?php

declare(strict_types=1);

namespace App\Model;

use Padhie\TwitchApiBundle\Response\Channels\Channel;
use Padhie\TwitchApiBundle\Response\Users\User;

final class TwitchUserChannel
{
    private User $user;
    private Channel $channel;

    public function __construct(User $user, Channel $channel)
    {
        $this->user = $user;
        $this->channel = $channel;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }
}
