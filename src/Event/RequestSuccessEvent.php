<?php

namespace App\Event;

use App\Entity\Request;
use App\Entity\Config;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RequestSuccessEvent
 * @package App\Event
 */
class RequestSuccessEvent extends Event
{
    const NAME = 'request.success';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Config
     */
    protected $config;

    /**
     * RequestSuccessEvent constructor.
     *
     * @param Request $request
     * @param Config $config
     */
    public function __construct(Request $request, Config $config)
    {
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}
