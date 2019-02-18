<?php

namespace App\DataFixtures;

use App\Entity\Child;
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
        $user = new Child();
        $user
            ->setBirthdate((new \DateTime())->sub(new \DateInterval('P3Y')))
            ->setDiagnosis('Детский церебральный паралич. Спастический тетрапарез.')
            ->setImages(['https://sun1-13.userapi.com/c852024/v852024162/bd56b/X4T2yr0NFW0.jpg'])
            ->setComment(
                'Здравствуйте! Меня зовут Фамилия Имя, мне 25 лет. В 2016 году в нашей семье произошла беда. Моему сыну в год поставили страшный диагноз ДЦП, спастическая диплегия, гипоксически-ишемический генез, расходящееся косоглазие. Сопуствующим заболеванием является бронхиальная астма. Сейчас ему 2 года, он сам практически не сидит и не ходит. Ваню я воспитываю одна, папы у нас нет. (бросил нас, когда сын еще не родился). После первого посещения в РЦ Больница г.Россия он начал сам переворачиваться. Это уже достижение для нас. Если интенсивно лечиться и не запускать, то врачи прогнозируют нам выздоровление. Я очень надеюсь, что с вашей помощью мой сынок встанет на ножки!'
            )
            ->setGoal(20000)
            ->setCollected(8000)
            ->setName('Ваня Иванов');
        $manager->persist($user);
        $manager->flush();
    }
}
