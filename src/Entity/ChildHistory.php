<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChildHistoryRepository")
 * @ORM\Table(name="child_histroy")
 */
class ChildHistory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ChildHistory", inversedBy="children", fetch="LAZY")
     */
    private $childID;

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

    public function getChildID(): ?int
    {
        return $this->childID;
    }

    public function setChildID(int $childID): self
    {
        $this->childID = $childID;

        return $this;
    }

    public function getSun()
    {
        return $this->sun;
    }

    public function setSun($sun): self
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
