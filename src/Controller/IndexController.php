<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    private NotificationRepository $notificationRepository;
    private UserRepository $userRepository;
    private UserService $userService;
    private LoggerInterface $logger;

    public function __construct(
        NotificationRepository $notificationRepository,
        UserRepository $userRepository,
        UserService $userService,
        LoggerInterface $logger
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $currentUser = $this->userService->getCurrentUser();
        $notifications = $this->getNotifications($currentUser);

        $users = $currentUser->isAdmin() ? $this->userRepository->findAll() : [];
        $userWithoutNotifications = $this->getUserWithoutNotifications($users, array_keys($notifications));

        return $this->render('index/index.html.twig', [
            'notifications' => $notifications,
            'userWithoutNotifications' => $userWithoutNotifications,
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
     * @return array<string, list<Notification>>
     */
    private function getNotifications(User $currentUser): array
    {
        if ($currentUser->isAdmin()) {
            $notifications = $this->notificationRepository->findAll();
        } else {
            $notifications = $this->notificationRepository->findBy([
                'user' => $currentUser,
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

    /**
     * @param list<User> $users
     * @param list<string> $userWithNotifications
     * @return list<string>
     */
    private function getUserWithoutNotifications(array $users, array $userWithNotifications): array
    {
        $userWithoutNotifications = [];
        foreach ($users as $user) {
            $username = $user->getUsername();
            if (in_array($username, $userWithNotifications, true)) {
                continue;
            }

            $userWithoutNotifications[] = $username;
        }

        return $userWithoutNotifications;
    }
}
