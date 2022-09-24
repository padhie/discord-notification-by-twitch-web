<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment as TwigEnvironment;

final class UserPreloadListener
{
    private TwigEnvironment $twig;
    private UserService $userService;

    public function __construct(
        TwigEnvironment $twig,
        UserService $userService
    ) {
        $this->twig = $twig;
        $this->userService = $userService;
    }

    /**
     * @return array<string, array<int, string|int>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->twig->addGlobal('currentUser', $this->getUser());
    }

    private function getUser(): User
    {
        return $this->userService->getCurrentUser();
    }
}
