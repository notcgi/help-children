<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 * @ORM\Table(name="documents")
 */
class Document
{
    const TYPES = [
        'Финансовые отчёты'      => 'financial',
        'Аудиторские заключения' => 'auditor'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $category;

    /**
     * @ORM\Column(type="string")
     */
    private $file;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filesize;

    public function __construct() {}

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return (string) $this->title; }
    public function setTitle(string $title): self {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string { return (string) $this->description; }
    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    public function getCategory(): string { return (string) $this->category; }
    public function setCategory(string $category): self {
        $this->category = $category;
        return $this;
    }

    public function getFile(): string { return (string) $this->file; }
    public function setFile(string $file): self {
        $this->file = $file;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function getTextDate()
    {
        return $this->date ? $this->date->format('d.m.Y') : '';
    }

    public function getNameDate()
    {
        $names = ['','январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь'];

        return $this->date ? $names[(int) $this->date->format('m')].' '.$this->date->format('Y') : '';
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFilesize(): ?string
    {
        $filesize=filesize('../public'.$this->file);
        $formats = array('Б','КБ','МБ','ГБ','ТБ');
        $format = 0;
        while ($filesize > 1024 && count($formats) != ++$format)
        {
            $filesize = round($filesize / 1024, 2);
        }
        $formats[] = 'ТБ';
        
        return $filesize.' '.$formats[$format]; 
        return filesize('../public'.$this->file);
    }

    public function setFilesize(?string $filesize): self
    {
        $this->filesize = $filesize;

        return $this;
    }
}
