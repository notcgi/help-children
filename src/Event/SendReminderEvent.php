<?php

namespace App\Event;
use Symfony\Component\EventDispatcher\Event;
/**
 * Class EmailConfirm
 * @package App\Event
 */
class SendReminderEvent extends Event
{
    const NAME = 'sendReminder';

    private $email;
    private $name;
    private $date;
    private $lastName;
    private $phone;
    private $code;
    private $today;

    function __construct($email, $name, $date, $lastName, $phone, $code, $today) {
        $this->email = $email;
        $this->name = $name;
        $this->date = $date;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->code = $code;
        $this->today = $today;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getName() {
        return $this->name;
    }

    public function getDate() {
        return $this->date;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getCode() {
        return $this->code;
    }

    public function getToday() {
        return $this->today;
    }
}
