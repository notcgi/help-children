<?php

namespace App\Event;

use App\Entity\User;
use App\Entity\Request;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class HalfYearRecurrentEvent
 * @package App\Event
 */
class HalfYearRecurrentEvent extends Event
{
    const NAME = 'halfYearRecurrent';
    /**
     * @var User
     */
    protected $req;

    /**
     * RegistrationEvent constructor.
     *
     * @param User $user
     */
    public function __construct(Request $req)
    {
        $this->req = $req;
    }
    public function getRequest(): Request
    {
        return $this->req;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->req->getUser();
    }
}
