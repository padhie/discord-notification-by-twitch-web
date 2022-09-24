<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Setting as SettingEntity;
use DateTimeImmutable;

final class Setting
{
    public bool $notificationActive = false;
    public ?string $notificationInactiveUntil = null;

    private function __construct()
    {
    }

    /**
     * @param SettingEntity[] $settings
     */
    public static function createFromEntities(array $settings): self
    {
        $self = new self();

        foreach ($settings as $setting) {
            $key = $setting->getKey();
            $value = self::convertCorrectValue($setting);

            $self->{$key} = $value;
        }

        return $self;
    }

    /**
     * @return bool|DateTimeImmutable|null
     */
    private static function convertCorrectValue(SettingEntity $setting)
    {
        if ($setting->getKey() === 'notificationActive') {
            return in_array($setting->getValue(), ['1', 'true']);
        }

        if ($setting->getKey() === 'notificationInactiveUntil') {
            return new DateTimeImmutable($setting->getValue());
        }

        return null;
    }
}
