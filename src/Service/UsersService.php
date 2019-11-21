<?php

namespace App\Service;

use App\Entity\SendGridSchedule;
use App\Entity\User;
use App\Entity\Child;
use App\Event\RegistrationEvent;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    private $generator;

    public $request;

    public $session;

    public $passwordEncoder;

    public $dispatcher;

    public function __construct(
        ManagerRegistry $doctrine,
        SessionInterface $session,
        UrlGeneratorInterface $generator,
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $dispatcher
    ) {
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->passwordEncoder = $passwordEncoder;
        $this->dispatcher = $dispatcher;
        $this->generator = $generator;
    }

    /**
     * @param array $data
     *
     * @return User
     * @throws \Exception
     * @throws \RuntimeException
     */
    public function findOrCreateUser(array $data)
    {
        if (!isset($data['email'])) {
            throw new \RuntimeException('Email undefined');
        }

        $userRepository = $this->doctrine->getRepository(User::class);
        /** @var User $user */
        $user = $userRepository->findOneBy(['email' => $data['email']]);

        $childs= $this->doctrine->getRepository(\App\Entity\Child::class)->getOpened();
        $chnames=[];
        foreach ($childs as $child) {
            $chnames[]=$child->getName();
        }
        shuffle($chnames);
        $chnames=array_slice($chnames, 0, 3);
        if ($user) {
            $entityManager = $this->doctrine->getManager();            
    
            // Завершение платежа
            $entityManager->persist(
                (new SendGridSchedule())
                ->setEmail($user->getEmail())
                ->setName($user->getFirstName())
                ->setBody( [
                    'first_name' => $user->getFirstName(),
                    'childs' => implode("<br>", $chnames),
                    'url' => $user->getDonateUrl()
                ])
                ->setTemplateId('d-a48d63b8f41c4020bd112a9f1ad31426')
                ->setSendAt(
                    \DateTimeImmutable::createFromMutable(
                        (new \DateTime())
                        ->add(new \DateInterval('PT1H30M'))                            
                    )
                )                    
            );
            $entityManager->flush();

            // return [$user,False];
        }
        $puser = (isset($data['phone']) && strlen($data['phone'])>5) ? $this->doctrine->getManager()->createQuery("SELECT u FROM App\\Entity\\User u WHERE JSON_VALUE(u.meta, '$.phone') = ". $data['phone'])->getOneOrNullResult() : null;
        if (isset($puser) and !$user) {
            $entityManager = $this->doctrine->getManager();
            // Завершение платежа
            $entityManager->persist(
                (new SendGridSchedule())
                ->setEmail($puser->getEmail())
                ->setName($puser->getFirstName())
                ->setBody( [
                    'first_name' => $puser->getFirstName(),
                    'childs' => implode("<br>", $chnames),
                    'url' => $puser->getDonateUrl()
                ])
                ->setTemplateId('d-a48d63b8f41c4020bd112a9f1ad31426')
                ->setSendAt(
                    \DateTimeImmutable::createFromMutable(
                        (new \DateTime())
                        ->add(new \DateInterval('PT1H30M'))                          
                    )
                )                    
            );
            $entityManager->flush();

            // return [$puser,False];
        }
        if (($user==$puser) && $user && $puser) return [$user,True];

        else if ($user && $puser==null)  return [$user, True];
        else if ($user)  return [$user, False];
        else if ($puser) return [$puser,False];
        else{

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
                ->setBody( [
                    'first_name' => $user->getFirstName(),
                    'childs' => implode("<br>", $chnames),
                    'url' => $user->getDonateUrl()
                ])
                ->setTemplateId('d-a48d63b8f41c4020bd112a9f1ad31426')
                ->setSendAt(
                    \DateTimeImmutable::createFromMutable(
                        (new \DateTime())
                        ->add(new \DateInterval('PT1H30M'))                          
                    )
                )                    
            );
            $entityManager->flush();

            $this->dispatcher->dispatch(new RegistrationEvent($user), RegistrationEvent::NAME);

            return [$user,True];
        }
    }
}
