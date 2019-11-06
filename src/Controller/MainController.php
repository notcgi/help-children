<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\User;
use App\Form\ResetPasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Event\EmailConfirm;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MainController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function main()
    {
        return $this->render('pages/main.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function contacts()
    {
        return $this->render('pages/contacts.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function sms()
    {
        return $this->render('pages/sms.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function docs()
    {
        return $this->render('pages/docs.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function help()
    {
        return $this->render('pages/help.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function partners()
    {
        return $this->render('pages/partners.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function reports(
        Request $request,
        EventDispatcherInterface $dispatcher,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $email = $request->query->get('email');
        $code = $request->query->get('code');

        if (isset($code)) {
            $doctrine = $this->getDoctrine();
            $user = $doctrine->getRepository(User::class)->findOneBy([
                'ref_code' => $code,
                'email' => $email
            ]);

            if ($user) {
                if ($user->getPass() == null) {
                    $title = 'Завершение регистрации';
                    $description = 'Для продолжения регистрации введите свой пароль';
                    $value = 'Продолжить';
                    
                    $form1 = $this->createForm(ResetPasswordFormType::class, $user);
                    $form1->handleRequest($request);

                    if (!$form1->isSubmitted()) {
                        return $this->render('auth/resetPassword.twig', 
                        ['form' => $form1->createView(), 'title' => $title, 'description' => $description, 'value' => $value]);
                    }                                 
                    // encode the plain password
                    $user->setPass(
                        $passwordEncoder->encodePassword(
                            $user,
                            $form1->get('password')->getData()
                        )
                    );                
                }

                if ($user) {
                    $doctrine->getManager()->persist($user->setRefCode(null));
                    $doctrine->getManager()->persist($user->setConfirmed(1));
                    $doctrine->getManager()->flush();
                    $this->addFlash('code_confirm', 'E-mail подтверждён');
                    $dispatcher->dispatch(new EmailConfirm($user), EmailConfirm::NAME);
                }
            }
        }
        return $this->render(
            'pages/reports.twig',
            [
                'financial' => $this->getDoctrine()->getRepository(Document::class)->findBy(
                    ['category' => 'financial'],
                    ['date' => 'DESC']),
                'auditor' => $this->getDoctrine()->getRepository(Document::class)->findBy(
                    ['category' => 'auditor'],
                    ['date' => 'DESC']),
            ]
        );
    }
}
