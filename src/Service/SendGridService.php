<?php

namespace App\Service;

use SendGrid\Mail\From;
use SendGrid\Mail\Mail;
use SendGrid\Mail\To;
use SendGrid\Response;

/**
 * Class SendGridService
 * @package App\Service
 */
class SendGridService
{
    /**
     * @var \SendGrid
     */
    private $send_grid;

    /**
     * @var From
     */
    private $from;

    public function __construct($api_key, $from)
    {
        $this->send_grid = new \SendGrid($api_key);
        $this->from = new From($from);
    }

    /**
     * @return \SendGrid
     */
    public function getSendGrid(): \SendGrid
    {
        return $this->send_grid;
    }

    /**
     * @param Mail $mail
     *
     * @return Response
     */
    public function send(Mail $mail): Response
    {
        return $this->send_grid->send($mail);
    }

    /**
     * @param string      $to
     * @param string|null $name
     * @param array|null  $params
     * @param string|null $subject
     *
     * @return Mail
     */
    public function getMail(
        string $to,
        string $name = null,
        array $params = null,
        string $subject = null
    ): Mail {
        return new Mail($this->from, new To($to, $name, $params, $subject), $subject);
    }

    /**
     * @param string      $to
     * @param string|null $name
     * @param array|null  $params
     * @param string|null $subject
     *
     * @return Response
     */
    public function createAndSendMail(
        string $to,
        string $name = null,
        array $params = null,
        string $subject = null
    ): Response {
        return $this->send(new Mail($this->from, new To($to, $name, $params, $subject)));
    }
}
