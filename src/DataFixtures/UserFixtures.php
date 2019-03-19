<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setEmail('fond.detyam@mail.ru')
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstName('fond')
            ->setLastName('detyam')
            ->setPass(
                $this->passwordEncoder->encodePassword(
                    $user,
                    'Fond1234'
                )
            );
        $manager->persist($user);
        $manager->flush();
    }
}
