<?php

namespace App\Event;

use App\Entity\Request;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class FirstRequestSuccessEvent
 * @package App\Event
 */
class FirstRequestSuccessEvent extends Event
{
    const NAME = 'request.successFirst';

    const RECURRING_NAME = 'recurringRequest.successFirst';

    /**
     * @var Request
     */
    protected $request;

    /**
     * FirstRequestSuccessEvent constructor.
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
