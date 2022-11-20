<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

final class PageDetectorSubscriber implements EventSubscriberInterface
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $page = $this->detectPageFromUri($event->getRequest()->getRequestUri());
        $this->twig->addGlobal('page', $page);
    }

    private function detectPageFromUri(string $uri): string
    {
        if (strpos($uri, '/user') === 0) {
            return 'users';
        }

        if (strpos($uri, '/settings') === 0) {
            return 'settings';
        }

        return 'home';
    }
}