<?php

declare(strict_types=1);

namespace App\Service\Updater\Streamer;

use App\Entity\Notification;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManager;

final class V3Updater implements StreamUpdateInterface
{
    private UserService $userService;
    private EntityManager $entityManager;

    public function __construct(
        UserService $userService,
        EntityManager $entityManager
    ) {
        $this->userService = $userService;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function check(array $content): bool
    {
        if (!isset($content['version'])) {
            return false;
        }

        return (int) $content['version'] >= 3;
    }

    /**
     * {@inheritDoc}
     */
    public function update(array $content): array
    {
        foreach ($content['items'] as $user => $items) {
            $this->insertPerUser($user, $items);
        }

        return [
            'version' => 3,
            'items' => $content['items'],
        ];
    }

    /**
     * @param array<int, array<string, string>> $items
     */
    private function insertPerUser(string $username, array $items): void
    {
        $user = $this->userService->getDefaultUser();

        foreach ($items as $item) {
            $this->insertNotification($user, $item);
        }

        $this->entityManager->flush();
    }

    /**
     * @param array<string, string> $item
     */
    private function insertNotification(User $user, array $item): void
    {
        $notification = new Notification($user);
        $notification
            ->setChannel($item['channel'])
            ->setWebHook($item['webHook'])
            ->setMessage($item['message']);

        $this->entityManager->persist($notification);
    }
}
