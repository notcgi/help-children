<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SendGridScheduleRepository")
 * @ORM\Table(name="sendGrid_schedule")
 */
class SendGridSchedule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $template_id;

    /**
     * @ORM\Column(type="json", nullable=false, options={"collation":"utf8mb4_bin"})
     */
    private $body = [];

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $sendAt;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $sent = 0;


    /**
     * @return bool
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @param bool $sent
     * 
     * @return SendGridSchedule
     */
    public function setSent(bool $sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): ? int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return SendGridSchedule
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ? string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return SendGridSchedule
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ? string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return SendGridSchedule
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateId(): ? string
    {
        return $this->template_id;
    }

    /**
     * @param string $template_id
     *
     * @return SendGridSchedule
     */
    public function setTemplateId(string $template_id): self
    {
        $this->template_id = $template_id;

        return $this;
    }

    /**
     * @return mixed[]|null
     */
    public function getBody(): ? array
    {
        return $this->body;
    }

    /**
     * @param mixed[] $body
     *
     * @return SendGridSchedule
     */
    public function setBody(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getSendAt(): ? \DateTimeImmutable
    {
        return $this->sendAt;
    }

    /**
     * @param \DateTimeImmutable $sendAt
     *
     * @return SendGridSchedule
     */
    public function setSendAt(\DateTimeImmutable $sendAt): self
    {
        $this->sendAt = $sendAt;

        return $this;
    }
}
