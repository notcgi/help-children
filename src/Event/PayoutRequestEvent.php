<?php

namespace App\Event;

/**
 * Class PayoutRequestEvent
 * @package App\Event
 */
class PayoutRequestEvent extends RegistrationEvent
{
    const NAME = 'account.payoutRequestEvent';
}
