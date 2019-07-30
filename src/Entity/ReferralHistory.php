<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReferralHistoryRepository")
 * @ORM\Table(name="referral_history")
 */
class ReferralHistory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Request", inversedBy="referral_history", fetch="LAZY")
     * @ORM\JoinColumn(name="request_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $request;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="referral_history", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="donate_history", fetch="LAZY")
     * @ORM\JoinColumn(name="donator_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $donator;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $sum = .0;

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

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     *
     * @return ReferralHistory
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getDonator(): User
    {
        return $this->donator;
    }

    /**
     * @param User $donator
     *
     * @return ReferralHistory
     */
    public function setDonator(User $donator): self
    {
        $this->donator = $donator;

        return $this;
    }

    public function getSum()
    {
        return $this->sum;
    }

    public function setSum($sum): self
    {
        $this->sum = $sum;

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
