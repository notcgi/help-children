<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChildRepository")
 * @ORM\Table(name="counters")
 */
class Counter
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=32)
     */
    private $type;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"collation":"utf8mb4_unicode_ci"})
     */
    private $additional_value;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * Counter constructor.
     *
     * @param string      $type
     * @param int         $value
     * @param string|null $additional_value
     *
     * @throws \Exception
     */
    public function __construct(string $type, int $value = 0, string $additional_value = null)
    {
        $this->type = $type;
        $this->value = $value;
        $this->additional_value = $additional_value;
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return string|null
     */
    public function getType(): ? string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Counter
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getValue(): ? int
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @return Counter
     */
    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAdditionalValue(): ? string
    {
        return $this->additional_value;
    }

    /**
     * @param string $additional_value
     *
     * @return Counter
     */
    public function setAdditionalValue(string $additional_value): self
    {
        $this->additional_value = $additional_value;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ? \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return Counter
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
