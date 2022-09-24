<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;

final class UserService
{
    private const DEFAULT_USERNAME = 'anonymous';

    private Security $security;
    private UserRepository $userRepository;

    public function __construct(
        Security $security,
        UserRepository $userRepository
    ) {
        $this->security = $security;
        $this->userRepository = $userRepository;
    }

    public function getCurrentUser(bool $useDefault = true): User
    {
        $user = $this->security->getUser();
        if ($user === null) {
            throw new \RuntimeException('No current user found');
        }

        if ($user instanceof User) {
            return $user;
        }

        if ($useDefault === true) {
            return $this->getDefaultUser();
        }

        $entityUser = $this->userRepository->findOneBy([
            'username' => $user->getUsername(),
        ]);

        if ($entityUser instanceof User) {
            return $entityUser;
        }

        throw new \RuntimeException(sprintf(
            'No user with name %s found',
            $user->getUsername()
        ));
    }

    public function getDefaultUser(): User
    {
        $user = $this->userRepository->findOneByUsername(self::DEFAULT_USERNAME);
        assert($user instanceof User);

        return $user;
    }
}
