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
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id")
     */
    private $child;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $sun;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChildId(): ?Child
    {
        return $this->child;
    }

    public function setChildID(Child $child): self
    {
        $this->child = $child;

        return $this;
    }

    public function getSun(): float
    {
        return $this->sun;
    }

    public function setSun(float $sun): self
    {
        $this->sun = $sun;

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