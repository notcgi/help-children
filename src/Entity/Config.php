<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfigRepository")
 */
class Config
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=2)
     */
    private $percentDefault;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=2)
     */
    private $percentRecurrent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPercentDefault():float
    {
        return $this->percentDefault;
    }

    public function setPercentDefault(float $percentDefault): self
    {
        $this->percentDefault = $percentDefault;

        return $this;
    }

    public function getPercentRecurrent():float
    {
        return $this->percentRecurrent;
    }

    public function setPercentRecurrent(float $percentRecurrent): self
    {
        $this->percentRecurrent = $percentRecurrent;

        return $this;
    }
}
