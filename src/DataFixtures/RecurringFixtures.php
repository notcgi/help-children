<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Request;
use App\Entity\RecurringPayment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RecurringFixtures extends Fixture
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
            ->setEmail('user4@mail.com')
            ->setFirstName('Stupid 4')
            ->setLastName('User')
            ->setPass(
                $this->passwordEncoder->encodePassword(
                    $user,
                    'user44'
                )
            );
        $manager->persist($user);
        $req = new Request();
        $req->setUser($user)
            ->setSum(300)
            ->setStatus(2)
            ->setUpdatedAt(new \DateTime());
        $manager->persist($req);
        $recurring = new RecurringPayment();
        $recurring->setRequest($req);
        $manager->persist($recurring);
        $manager->flush();
    }
}
