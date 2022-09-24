<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Service\UserService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    private NotificationRepository $notificationRepository;
    private UserService $userService;
    private LoggerInterface $logger;

    public function __construct(
        NotificationRepository $notificationRepository,
        UserService $userService,
        LoggerInterface $logger
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->userService = $userService;
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $user = $this->userService->getCurrentUser();

        return $this->render('index/index.html.twig', [
            'notifications' => $this->getNotifications($user),
        ]);
    }

    /**
     * @Route("/logger", name="logger_test")
     */
    public function logger(): JsonResponse
    {
        $this->logger->debug('debug log');
        $this->logger->info('info log');
        $this->logger->notice('notice log');
        $this->logger->warning('warning log');
        $this->logger->error('error log');
        $this->logger->critical('critical log');
        $this->logger->alert('alert log');
        $this->logger->emergency('emergency log');

        return new JsonResponse([
            'state' => 'success',
        ]);
    }

    /**
     * @return array<string, array<int, Notification>>
     */
    private function getNotifications(User $user): array
    {
        if ($user->isAdmin()) {
            $notifications = $this->notificationRepository->findAll();
        } else {
            $notifications = $this->notificationRepository->findBy([
                'user' => $user,
            ]);
        }

        $groupedNotifications = [];
        foreach ($notifications as $notification) {
            $owner = $notification->getUser()->getUsername();
            if (!array_key_exists($owner, $groupedNotifications)) {
                $groupedNotifications[$owner] = [];
            }

            $groupedNotifications[$owner][] = $notification;
        }

        return $groupedNotifications;
    }
}
