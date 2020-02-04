<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChTargetRepository")
 */
class ChTarget
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $child;

    /**
     * @ORM\Column(type="boolean")
     */
    private $rehabilitation;

    /**
     * @ORM\Column(type="integer")
     */
    private $goal;

    /**
     * @ORM\Column(type="integer")
     */
    private $collected;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $totime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=65500, nullable=true)
     */
    private $descr;

    /**
     * @ORM\Column(type="string", length=65500, nullable=true)
     */
    private $attach;

    /**
     * @ORM\Column(type="boolean")
     */
    private $allowclose;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChild(): ?int
    {
        return $this->child;
    }

    public function setChild(int $child): self
    {
        $this->child = $child;

        return $this;
    }

    public function getRehabilitation(): ?bool
    {
        return $this->rehabilitation;
    }

    public function setRehabilitation(bool $rehabilitation): self
    {
        $this->rehabilitation = $rehabilitation;

        return $this;
    }

    public function getGoal(): ?int
    {
        return $this->goal;
    }

    public function setGoal(int $goal): self
    {
        $this->goal = $goal;

        return $this;
    }

    public function getCollected(): ?int
    {
        return $this->collected;
    }

    public function setCollected(int $collected): self
    {
        $this->collected = $collected;

        return $this;
    }

    public function getTotime(): ?\DateTime
    {
        return $this->totime;
    }

    public function setTotime(?\DateTime $totime): self
    {
        $this->totime = $totime;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescr(): ?string
    {
        return $this->descr;
    }

    public function setDescr(?string $descr): self
    {
        $this->descr = $descr;

        return $this;
    }

    public function getAttach()//: ?string
    {
        return $this->attach;
    }
    public function getImg()//: ?string
    {
        return json_decode($this->attach)[0] ?? '';
    }

    public function setAttach($attach): self
    {
        $this->attach = $attach;

        return $this;
    }
    public function getAllowClose()
    {
        return $this->allowclose ?? 1;
    }

    public function setAllowClose($allowclose): self
    {
        $this->allowclose = $allowclose;

        return $this;
    }
}
