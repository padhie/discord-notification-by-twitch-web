<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass=SettingRepository::class)
 */
class Setting
{
    public const NOTIFICATION_ACTIVE = 'notificationActive';
    public const NOTIFICATION_INACTIVE_UNTIL = 'notificationInactiveUntil';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="`key`", type="string", length=255, nullable=false)
     */
    private string $key;

    /**
     * @ORM\Column(name="`value`", type="string", length=255, nullable=false)
     */
    private string $value;

    final public function getId(): int
    {
        return $this->id;
    }

    final public function getKey(): string
    {
        return $this->key;
    }

    final public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    final public function getValue(): string
    {
        return $this->value;
    }

    final public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
