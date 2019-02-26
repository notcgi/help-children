<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

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
    public function reports()
    {
        return $this->render('pages/reports.twig');
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \UnexpectedValueException
     */
    public function donate(Request $request)
    {
        $form_errors = [];
        $form = [
            'child_id' => trim($request->request->filter('child_id', null, FILTER_VALIDATE_INT)),
            'fullName' => trim($request->request->get('fullName', '')),
            'phone' => trim($request->request->get('phone', '')),
            'email' => trim($request->request->get('email', '')),
            'sum' => (int) $request->request->filter('sum', 100, FILTER_VALIDATE_INT),
            'sumOther' => (int) $request->request->filter('sumOther', '', FILTER_VALIDATE_INT),
            'recurent' => (bool) $request->request->get('recurent', true),
            'agree' => (bool) $request->request->get('agree', true),
        ];

        if ($request->isMethod('post')) {
            $form_errors = $this->validate($form);

            if (!count($form_errors)) {
                $user = $this->getDoctrine()->getRepository(RequestRepository::class)->findBy(
                    ['email' => $form['email']]
                );

                if (!$user) {
                    $user = new User();
                    $names = explode(' ', $form['fullName']);
                    $names_count = count($names);

                    if (2 == $names_count) {
                        $user->setFirstName(trim($names[1]));
                    }

                    if (3 == $names_count) {
                        $user->setMiddleName(trim($names[2]));
                    }

                    $user->setLastName(trim($names[0]))->setEmail($form['email'])->setPhone($form['phone']);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                }
            }
        }

        return $this->render('pages/donate.twig', ['form' => $form, 'formErrors' => $form_errors]);
    }

    /**
     * @param array $data
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    private function validate(array $data)
    {
        return Validation::createValidator()->validate(
            $data,
            new Assert\Collection(
                [
                    'child_id' => new Assert\GreaterThan(['value' => 0]),
                    'fullName' => new Assert\Length(['min' => 8, 'max' => 256]),
                    'phone' => new Assert\Regex(['pattern' => '/^\+?\d{10,13}$/i']),
                    'email' => new Assert\Email(),
                    'sum' => new Assert\Choice([100, 500]),
                    'sumOther' => new Assert\Range(['min' => 0, 'max' => 1000000]),
                    'recurent' => new Assert\Type(['type' => 'boolean']),
                    'agree' => new Assert\IsTrue(),
                ]
            )
        );
    }
}
