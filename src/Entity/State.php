<?php

namespace App\Entity;

use App\Repository\StateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="state")
 * @ORM\Entity(repositoryClass=StateRepository::class)
 */
class State
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="channel", type="string", length=255, nullable=false)
     */
    private string $channel;

    /**
     * @ORM\Column(name="online", type="boolean", nullable=false, options={"default":false})
     */
    private bool $online = false;

    final public function getId(): int
    {
        return $this->id;
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

    final public function isOnline(): bool
    {
        return $this->online;
    }

    final public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }
}
