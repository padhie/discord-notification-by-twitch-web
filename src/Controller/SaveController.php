<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SaveController extends AbstractController
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository,
        private readonly UserRepository $userRepository,
        private readonly UserService $userService
    ) {
    }

    /**
     * @Route("/save", name="save")
     */
    public function index(Request $request): Response
    {
        $currentUser = $this->userService->getCurrentUser();
        $streamers = $request->get('streamer', []);
        $newStreams = $request->get('newStreamer', []);

        $this->save($streamers);
        $this->delete($currentUser, $streamers);
        $this->new($currentUser, $newStreams);

        $this->addFlash('success', 'streamer save');

        return $this->redirectToRoute('home');
    }

    /**
     * @param array<int, array<string, string|null>> $data
     */
    private function save(array $data): void
    {
        foreach ($data as $id => $row) {
            $notification = $this->notificationRepository->find($id);
            if (!$notification instanceof Notification) {
                continue;
            }

            if (!isset($row['channel'])) {
                continue;
            }

            $notification
                ->setChannel($row['channel'])
                ->setWebHook($row['webHook'] ?? '')
                ->setMessage($row['message'] ?? '');

            $this->notificationRepository->saveEntity($notification);
        }
    }

    /**
     * @param array<int, string> $data
     */
    private function delete(User $currentUser, array $data): void
    {
        $idsSend = array_keys($data);

        $existingNotifications = $currentUser->isAdmin()
            ? $this->notificationRepository->findAll()
            : $this->notificationRepository->findBy(['user' => $currentUser]);

        $existingIds = array_map(
            static function (Notification $notification): int {
                return $notification->getId();
            },
            $existingNotifications
        );

        $idsToDelete = array_diff($existingIds, $idsSend);

        if ($currentUser->isAdmin()) {
            $this->notificationRepository->deleteByIds($idsToDelete);

            return;
        }

        $this->notificationRepository->deleteForUserByIds(
            (int) $currentUser->getId(),
            $idsToDelete
        );
    }

    /**
     * @param array<string, array<int, string>> $data
     */
    private function new(User $currentUser, array $data): void
    {
        $channels = $data['channel'] ?? [];
        $webHooks = $data['webHook'] ?? [];
        $messages = $data['message'] ?? [];

        foreach ($channels as $index => $channel) {
            $webHook = $webHooks[$index];
            $message = $messages[$index];

            $notification = (new Notification($currentUser))
                ->setChannel($channel)
                ->setWebHook($webHook)
                ->setMessage($message);

            $this->notificationRepository->saveEntity($notification);
        }

        $this->userRepository->saveEntity($currentUser);
    }
}
