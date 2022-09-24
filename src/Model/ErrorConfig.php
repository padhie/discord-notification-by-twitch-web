<?php

declare(strict_types=1);

namespace App\Model;

class ErrorConfig
{
    private string $project;
    private string $discordUrl;

    public function __construct(string $project, string $discordUrl)
    {
        $this->project = $project;
        $this->discordUrl = $discordUrl;
    }

    public function getProject(): string
    {
        return $this->project;
    }

    public function getDiscordUrl(): string
    {
        return $this->discordUrl;
    }
}
