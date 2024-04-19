<?php

namespace App\Entity;

use App\Repository\ParsedNewsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ParsedNewsRepository::class)]
class ParsedNews
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('read')]
    private ?string $name = null;

    #[ORM\Column(length: 10000, nullable: true)]
    #[Groups('read')]
    private ?string $value = null;

    #[ORM\Column(length: 255)]
    #[Groups('read')]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('read')]
    private ?string $source_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('read')]
    private ?string $source_site = null;

    #[ORM\Column]
    #[Groups('read')]
    private int $rating = 0;

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;
        return $this;
    }

    public function __construct()
    {
        $this->rating = mt_rand(1, 10);
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return substr($this->value, 0, 200);
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getSourceUrl(): ?string
    {
        return $this->source_url;
    }

    public function getSourceSite(): ?string
    {
        return $this->source_site;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setSourceUrl(?string $source_url): static
    {
        $this->source_url = $source_url;

        return $this;
    }

    public function setSourceSite(?string $source_site): static
    {
        $this->source_site = $source_site;

        return $this;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }
}
