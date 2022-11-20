<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $password = $this->passwordEncoder->encodePassword($user, 'admin');

        $user->setUsername('admin')
            ->setPassword($password)
            ->setRoles([
                'ROLE_ADMIN',
                'ROLE_USER',
            ]);

        $manager->persist($user);
        $manager->flush();

        $this->addReference('dev-user', $user);
    }
}
