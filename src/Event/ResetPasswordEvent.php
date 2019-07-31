<?php

namespace App\Event;

/**
 * Class ResetPasswordEvent
 * @package App\Event
 */
class ResetPasswordEvent extends RegistrationEvent
{
    const NAME = 'user.resetPassword';
}
