<?php

namespace App\DataFixtures;

use App\Entity\Request;
use App\Entity\ReferralHistory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ReferralHistoryFixtures extends Fixture
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
            ->setEmail('user2@mail.com')
            ->setFirstName('Stupid ')
            ->setLastName('User')
            ->setPass(
                $this->passwordEncoder->encodePassword(
                    $user,
                    'user13'
                )
            );
        $manager->persist($user);
        $req = new Request();
        $req->setUser($user)
            ->setSum(300)
            ->setStatus(2)
            ->setUpdatedAt(new \DateTime());
        $manager->persist($req);
        $rh = new ReferralHistory();
        $rh->setSum(10)->setUser($user)->setRequest($req)->setDonator($user);
        $manager->persist($rh);
        $manager->flush();
    }
}
