<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\State;

final class LastStateService
{
    private const FILE = 'data/last_state.json';

    private string $baseDirectory;

    public function __construct(string $baseDirectory)
    {
        $this->baseDirectory = $baseDirectory;
    }

    public function getState(): State
    {
        $fullFilePath = $this->getFullFilePath();
        if (!file_exists($fullFilePath)) {
            return State::createFromArray([]);
        }

        $state = file_get_contents($fullFilePath);
        if ($state === false) {
            throw new \RuntimeException(
                sprintf('No file \'%s\' found.', $fullFilePath)
            );
        }

        $state = json_decode($state, true, 512, JSON_THROW_ON_ERROR);

        return State::createFromArray($state);
    }

    public function saveState(State $state): void
    {
        $content = json_encode($state, JSON_THROW_ON_ERROR);
        file_put_contents($this->getFullFilePath(), $content);
    }

    private function getFullFilePath(): string
    {
        return $this->baseDirectory . DIRECTORY_SEPARATOR . self::FILE;
    }
}
