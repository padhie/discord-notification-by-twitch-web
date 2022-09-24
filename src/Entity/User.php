<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Security\UserRoleConstants;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $username = '';

    /**
     * @var array<mixed>
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password = '';

    /**
     * @var Collection<int, Notification>
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="user")
     */
    private Collection $notifications;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    final public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    final public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    final public function getUserIdentifier(): string
    {
        return $this->username;
    }

    final public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return array<mixed>
     */
    final public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    /**
     * @param array<mixed> $roles
     */
    final public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    final public function getPassword(): string
    {
        return $this->password;
    }

    final public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    final public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    final public function eraseCredentials(): void
    {
    }

    final public function isAdmin(): bool
    {
        return in_array(UserRoleConstants::ADMIN, $this->roles, true);
    }

    final public function getNotifications(): Collection
    {
        $this->fixNotifications();

        return $this->notifications;
    }

    final public function addNotification(Notification $notification): self
    {
        $this->fixNotifications();
        if ($this->notifications->contains($notification)) {
            return $this;
        }

        $notification->setUser($this);
        $this->notifications->add($notification);

        return $this;
    }

    final public function removeNotification(Notification $notification): self
    {
        $this->fixNotifications();
        $this->notifications->removeElement($notification);

        return $this;
    }

    private function fixNotifications(): void
    {
        if (isset($this->notifications)) {
            return;
        }

        $this->notifications = new ArrayCollection();
    }
}
