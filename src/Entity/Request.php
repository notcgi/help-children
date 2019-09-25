<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RequestRepository")
 * @ORM\Table(name="requests")
 */
class Request
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64, options={"collation":"utf8mb4_unicode_ci"})
     */
    private $order_id;

    /**
     * @ORM\ManyToOne(targetEntity="Child", inversedBy="history", fetch="LAZY")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $child;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="requests", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $sum = .0;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true})
     */
    private $status = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $recurent = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToOne(targetEntity="ReferralHistory", mappedBy="request", fetch="LAZY", cascade={"persist", "remove"})     
     */
    private $referral_history;

    /**
     * @ORM\Column(type="text", nullable=true, options={"collation":"utf8mb4_unicode_ci"})
     */
    private $json;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $TransactionId;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, options={"collation":"utf8mb4_unicode_ci"})
     */
    private $SubscriptionsId;

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

    public function getOrder_id(): ?string
    {
        return $this->order_id;
    }

    public function setOrder_id($id)
    {
        $this->order_id = $id;
        return $this;
    }

    public function getChild(): ?Child
    {
        return $this->child;
    }

    /**
     * @param Child|null $child
     *
     * @return Request
     */
    public function setChild($child): self
    {
        $this->child = $child;

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

    public function getSum(): float
    {
        return $this->sum;
    }

    public function setSum(float $sum): self
    {
        $this->sum = $sum;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setTransactionId(int $TransactionId): self
    {
        $this->TransactionId = $TransactionId;

        return $this;
    }

    public function getTransactionId(): ?int
    {
        return $this->TransactionId;
    }

    public function setSubscriptionsId($SubscriptionsId): self
    {
        $this->SubscriptionsId = $SubscriptionsId;

        return $this;
    }

    public function getSubscriptionsId()
    {
        return $this->SubscriptionsId;
    }

    public function setJson($Json): self
    {
        $this->json = $Json;

        return $this;
    }

    public function getJson()
    {
        return $this->json;
    }

    public function isRecurent(): ?bool
    {
        return $this->recurent;
    }

    public function setRecurent(bool $recurent): self
    {
        $this->recurent = $recurent;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    /**
     * @return ReferralHistory
     */
    public function getReferralHistory(): ?ReferralHistory
    {
        return $this->referral_history;
    }

    /**
     * @param ReferralHistory $referral_history
     *
     * @return Request
     */
    public function setReferralHistory(ReferralHistory $referral_history): self
    {
        $this->referral_history = $referral_history;

        return $this;
    }
}
