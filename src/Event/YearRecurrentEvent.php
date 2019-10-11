<?php

namespace App\Event;

use App\Entity\User;
use App\Entity\Request;
use Symfony\Component\EventDispatcher\Event;
/**
 * Class YearRecurrentEvent
 * @package App\Event
 */
class YearRecurrentEvent extends Event
{
    const NAME = 'yearRecurrent';
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
