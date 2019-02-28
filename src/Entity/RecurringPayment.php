<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecurringPaymentsRepository")
 * @ORM\Table(name="recurring_payments")
 */
class RecurringPayment
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Request", fetch="LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    private $request;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="requestsPayments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $withdrawalAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * User constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getWithdrawalAt(): ?\DateTimeInterface
    {
        return $this->withdrawalAt;
    }

    public function setWithdrawalAt(?\DateTimeInterface $withdrawalAt): self
    {
        $this->withdrawalAt = $withdrawalAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
