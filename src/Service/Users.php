<?php
// src/Service/MessageGenerator.php
namespace App\Service;
use App\Entity\User;

class Users
{
    public function addUser($email, SessionInterface $session)
    {
        $userData = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($userData) {
            return $userData;
        }

        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(["ROLE_USER"]);

        if ($session->has('referral')) {
            $referrer = (int)$session->get('referral');
            $referrerData = $this->getDoctrine()->getRepository(User::class)->find($referrer);

            if ($referrerData && empty($userData->getReferrer())) {
                $user->setReferrer($referrer);
                $session->remove('referral');
            }
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
