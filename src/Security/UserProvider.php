<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    /** @var array<mixed> */
    private array $users;

    public function __construct(string $users)
    {
        $this->users = json_decode($users, true, 512, JSON_THROW_ON_ERROR);
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        if (!isset($this->users[$username])) {
            throw new UsernameNotFoundException('user does not exists or user/password are invalid', 1);
        }

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->users[$username]);

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        throw new UnsupportedUserException('not supported', 3);
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }

    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        throw new \Exception('not supported', 2);
    }
}
