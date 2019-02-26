<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChildRepository")
 * @ORM\Table(name="children")
 */
class Child
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="json")
     */
    private $body = [];

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $collected;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $goal;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * Child constructor.
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function setBody(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getDiagnosis(): string
    {
        return $this->body['diagnosis'] ?? '';
    }

    public function setDiagnosis(string $diagnosis): self
    {
        $this->body['diagnosis'] = $diagnosis;

        return $this;
    }

    public function getImages(): array
    {
        return $this->body['img'] ?? [];
    }

    public function setImages(array $images): self
    {
        $this->body['img'] = $images;

        return $this;
    }

    public function getComment(): string
    {
        return $this->body['comment'] ?? '';
    }

    public function setComment(string $comment): self
    {
        $this->body['comment'] = $comment;

        return $this;
    }

    public function getCollected()
    {
        return $this->collected;
    }

    public function setCollected($collected): self
    {
        $this->collected = $collected;

        return $this;
    }

    public function getGoal()
    {
        return $this->goal;
    }

    public function setGoal($goal): self
    {
        $this->goal = $goal;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

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

    public function getLeftGoal(): float
    {
        $left = $this->goal - $this->collected;

        return 0 < $left ? $left : 0;
    }

    /**
     * @return int
     *
     * @throws \Exception
     */
    public function getAge(): int
    {
        return (new \DateTime())->diff($this->birthdate)->y;
    }

    public function getGoalRatio(): float
    {
        return 0 === $this->goal ? 0 : $this->collected / $this->goal;
    }
}
