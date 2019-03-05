<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChildHistoryRepository")
 * @ORM\Table(name="child_history")
 */
class ChildHistory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Child", inversedBy="history", fetch="LAZY")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", nullable=false)
     */
    private $child;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="child_history", fetch="LAZY")
     * @ORM\JoinColumn(name="donator_id", referencedColumnName="id", nullable=false)
     */
    private $donator;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $sum;

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

    public function getChild(): ?Child
    {
        return $this->child;
    }

    public function setChild(Child $child): self
    {
        $this->child = $child;

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
     * @return ChildHistory
     */
    public function setDonator(User $donator): self
    {
        $this->donator = $donator;

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
