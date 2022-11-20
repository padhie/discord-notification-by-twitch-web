<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\User as UserEntity;

final class User
{
    public string $username;
    /** @var list<string> */
    public array $roles;
    public string $newPassword = '';

    private function __construct(){
    }

    public static function createEmpty(): self
    {
        $self = new self();

        $self->username = '';
        $self->roles = [];

        return $self;
    }

    public static function createFromEntity(UserEntity $entity): self
    {
        $self = new self();

        $self->username = $entity->getUsername();
        $self->roles = $entity->getRoles();

        return $self;
    }

    public function containsRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }
}