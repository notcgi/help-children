<?php

namespace App\Event;

use App\Entity\Request;
// use Symfony\Component\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\Event;
class PaymentFailure extends Event
{
    const NAME = 'payment.failure';

    /**
     * @var Request
     */
    protected $request;

    /**
     * PaymentFailure constructor.
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
