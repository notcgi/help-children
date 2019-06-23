<?php

namespace App\Event;

use App\Entity\RecurringPayment;
use Symfony\Component\EventDispatcher\Event;

class RecurringPaymentRemove extends Event
{
    const NAME = 'recurring_payment.remove';

    /**
     * @var RecurringPayment
     */
    protected $rp;

    /**
     * RecurringPaymentRemove constructor.
     *
     * @param RecurringPayment $rp
     */
    public function __construct(RecurringPayment $rp)
    {
        $this->rp = $rp;
    }

    /**
     * @return RecurringPayment
     */
    public function getRecurringPayment(): RecurringPayment
    {
        return $this->rp;
    }
}
