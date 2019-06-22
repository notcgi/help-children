<?php

namespace App\Event;

/**
 * Class EmailConfirm
 * @package App\Event
 */
class EmailConfirm extends RegistrationEvent
{
    const NAME = 'user.emailConfirm';
}
