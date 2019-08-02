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

    function __construct($email, $name, $date) {
        $this->email = $email;
        $this->name = $name;
        $this->date = $date;
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
}
