<?php

namespace App\DataFixtures;

use App\Entity\Setting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class SettingsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $setting = (new Setting())
            ->setKey('notificationActive')
            ->setValue('1');
        $manager->persist($setting);

        $setting = (new Setting())
            ->setKey('notificationInactiveUntil')
            ->setValue('1970-01-01 00:00:00');
        $manager->persist($setting);

        $manager->flush();
    }
}
