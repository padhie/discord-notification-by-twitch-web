<?php

declare(strict_types=1);

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionListener implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
            ConsoleEvents::ERROR => ['onConsoleError', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->logger->error(sprintf(
            'Message: %s\nCode: %s\nFile: %s\nLine: %s',
            $event->getThrowable()->getMessage(),
            $event->getThrowable()->getCode(),
            $event->getThrowable()->getFile(),
            $event->getThrowable()->getLine()
        ));
    }

    public function onConsoleError(ConsoleErrorEvent $event): void
    {
        $this->logger->error(sprintf(
            'Message: %s\nCode: %s\nFile: %s\nLine: %s',
            $event->getError()->getMessage(),
            $event->getError()->getCode(),
            $event->getError()->getFile(),
            $event->getError()->getLine()
        ));
    }
}
