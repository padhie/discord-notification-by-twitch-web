<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="notifications")
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notifications", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private User $user;

    /**
     * @ORM\Column(name="channel", type="string", length=255, nullable=false)
     */
    private string $channel;

    /**
     * @ORM\Column(name="web_hook", type="string", length=255, nullable=false)
     */
    private string $webHook;

    /**
     * @ORM\Column(name="message", type="string", length=255, nullable=false)
     */
    private string $message;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    final public function getId(): int
    {
        return $this->id;
    }

    final public function getUser(): User
    {
        return $this->user;
    }

    final public function setUser(User $user): self
    {
        if ($this->user === $user) {
            return $this;
        }

        $this->user->removeNotification($this);
        $this->user = $user;
        $this->user->addNotification($this);

        return $this;
    }

    final public function getChannel(): string
    {
        return $this->channel;
    }

    final public function setChannel(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    final public function getWebHook(): string
    {
        return $this->webHook;
    }

    final public function setWebHook(string $webHook): self
    {
        $this->webHook = $webHook;

        return $this;
    }

    final public function getMessage(): string
    {
        return $this->message;
    }

    final public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
