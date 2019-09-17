<?php

namespace App\Event;

use App\Entity\Request;
use Symfony\Component\EventDispatcher\Event;

class RecurringPaymentFailure extends Event
{
    const NAME = 'recurring_payment.failure';

    /**
     * @var Request
     */
    protected $request;

    /**
     * RecurringPaymentFailure constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
