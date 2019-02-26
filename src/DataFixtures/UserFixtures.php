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
            ->setEmail('admin@mail.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstName('Stupid')
            ->setLastName('Admin')
            ->setPass(
                $this->passwordEncoder->encodePassword(
                    $user,
                    'admin1'
                )
            );
        $manager->persist($user);
        $user = new User();
        $user
            ->setEmail('user@mail.com')
            ->setFirstName('Stupid')
            ->setLastName('User')
            ->setPass(
                $this->passwordEncoder->encodePassword(
                    $user,
                    'user12'
                )
            );
        $manager->persist($user);
        $manager->flush();
    }
}
