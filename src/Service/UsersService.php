<?php

namespace App\Service;

use App\Entity\SendGridSchedule;
use App\Entity\User;
use App\Event\RegistrationEvent;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersService
{
    /**
     * @param array                        $data
     * @param SessionInterface             $session
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EventDispatcherInterface     $dispatcher
     *
     * @return User
     * @throws \RuntimeException
     */
    public $doctrine;

    public $request;

    public $session;

    public $passwordEncoder;

    public $dispatcher;

    public function __construct(
        ManagerRegistry $doctrine,
        SessionInterface $session,
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $dispatcher
    ) {
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->passwordEncoder = $passwordEncoder;
        $this->dispatcher = $dispatcher;
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

        if (isset($data['ref_code'])) {
            $user->setRefCode($data['ref_code']);
        }

        if (isset($data['pass'])) {
            $user->setPass(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $data['pass']
                )
            );
        }

        if (isset($data['referral'])) {
            $referrerId = (int) $data['referral'];
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

        // Завершение платежа
        $entityManager->persist(
            (new SendGridSchedule())
            ->setEmail($user->getEmail())
            ->setName($user->getFirstName())
            ->setBody([
                'first_name' => $user->getFirstName()
            ])
            ->setTemplateId('d-a5e99ed02f744cb1b2b8eb12ab4764b5')
            ->setSendAt(
                \DateTimeImmutable::createFromMutable(
                    (new \DateTime())
                    ->add(new \DateInterval('PT2H'))                            
                )
            )                    
        );
        $entityManager->flush();

        $this->dispatcher->dispatch(new RegistrationEvent($user), RegistrationEvent::NAME);

        return $user;
    }
}
