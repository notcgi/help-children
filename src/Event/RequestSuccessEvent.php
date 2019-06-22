<?php

namespace App\Event;

use App\Entity\Request;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RequestSuccessEvent
 * @package App\Event
 */
class RequestSuccessEvent extends Event
{
    const NAME = 'request.success';

    const RECURRING_NAME = 'recurringRequest.success';

    /**
     * @var Request
     */
    protected $request;

    /**
     * RequestSuccessEvent constructor.
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
