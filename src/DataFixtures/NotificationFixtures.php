<?php

namespace App\DataFixtures;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NotificationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference('dev-user');
        assert($user instanceof User);

        $notification = new Notification($user);
        $notification
            ->setChannel('padhie')
            ->setWebHook('https://discordapp.com/api/webhooks/678981017251610655/z4iNMNSaQoRhg7wU5URTXyEgpiIAhRNP6ez3bw-GEN0_Q5y7Io677OYbo-Q5peqVZ3AU')
            ->setMessage('Hey, <@&659105181514072074> ! WIR SIND LIVE! Kommt ran und seit mit dabei, wir warten schon auf euch!');

        $manager->persist($notification);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
