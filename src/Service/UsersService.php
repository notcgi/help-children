<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersService
{
    /**
     * @param array                        $data
     * @param SessionInterface             $session
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return User
     * @throws \RuntimeException
     */
    public $doctrine;

    public $session;

    public $passwordEncoder;

    public function __construct(
        ManagerRegistry $doctrine,
        SessionInterface $session,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param array $data
     *
     * @return User
     * @throws \Exception
     * @throws \RuntimeException
     */
    public function findOrCreateUser(array $data): User
    {
        if (!isset($data['email'])) {
            throw new \RuntimeException('Email undefined');
        }

        $userRepository = $this->doctrine->getRepository(User::class);
        /** @var User $user */
        $user = $userRepository->findOneBy(['email' => $data['email']]);

        if ($user) {
            return $user;
        }

        $user = new User();
        $user->setEmail($data['email'])
            ->setFirstName($data['name'] ?? '')
            ->setLastName($data['surname'] ?? '')
            ->setPhone($data['phone'] ?? '');

        if (isset($data['pass'])) {
            $user->setPass(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $data['pass']
                )
            );
        }

        if ($this->session->has('referral')) {
            $referrerId = (int) $this->session->get('referral');
            /** @var User $referrer */
            $referrer = $userRepository->find($referrerId);

            if ($referrer) {
                $user->setReferrer($referrer);
                $this->session->remove('referral');
            }
        }

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
