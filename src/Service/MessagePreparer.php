<?php

declare(strict_types=1);

namespace App\Service;

final class MessagePreparer
{
    private const PREFIX = '[TEST]';
    private const RANG_ANNOTATION_PATTERN = '/<@&\d+>/';
    private const GENERAL_ANNOTATIONS = [
        '@everyone' => '(at)everyone',
        '@here' => '(at)here',
    ];

    public function prepareForTesting(string $message): string
    {
        $message = $this->addPrefix($message);
        $message = $this->maskRangAnnotations($message);
        $message = $this->maskGeneralAnnotations($message);

        return trim($message);
    }

    private function addPrefix(string $message): string
    {
        return sprintf('%s %s', self::PREFIX, $message);
    }

    private function maskRangAnnotations(string $message): string
    {
        $matches = [];
        preg_match(self::RANG_ANNOTATION_PATTERN, $message, $matches);

        if (count($matches) === 0) {
            return $message;
        }

        $search = [];
        $replace = [];

        foreach ($matches as $match) {
            $search[] = $match;
            $replace[] = str_replace(['<@&', '>'], ['[@&', ']'], $match);
        }

        return str_replace($search, $replace, $message);
    }

    private function maskGeneralAnnotations(string $message): string
    {
        return str_replace(
            array_keys(self::GENERAL_ANNOTATIONS),
            array_values(self::GENERAL_ANNOTATIONS),
            $message
        );
    }
}
