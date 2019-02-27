<?php

namespace App\Service;

use App\Entity\User;
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
    public function findOrCreateUser(
        array $data,
        SessionInterface $session,
        UserPasswordEncoderInterface $passwordEncoder
    ): User {
        if (!isset($data['email'])) {
            throw new \RuntimeException('Email undefined');
        }

        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $data['email']]);

        if ($user) {
            return $user;
        }

        $user = new User();
        $user->setEmail($data['email'])
            ->setFirstName($data['firstName'] ?? '')
            ->setLastName($data['lastName'] ?? '')
            ->setMiddleName($data['middleName'] ?? '');

        if (isset($data['pass'])) {
            $user->setPass(
                $passwordEncoder->encodePassword(
                    $user,
                    $data['pass']
                )
            );
        }

        if ($session->has('referral')) {
            $referrerId = (int) $session->get('referral');
            $referrer = $userRepository->find($referrerId);

            if ($referrer) {
                $user->setReferrer($referrer);
                $session->remove('referral');
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
