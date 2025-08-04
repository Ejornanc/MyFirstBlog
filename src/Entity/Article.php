<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;

class Article
{
    private ?int $id = null;
    private ?string $title = null;
    private ?string $content = null;
    private ?DateTimeInterface $date = null;
    private ?string $slug = null;
    private ?bool $actif = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function getSlug(): ?string
    {
        if (!$this->slug) {
            // Generate a slug from the title if not set
            $this->slug = strtolower(str_replace(' ', '-', $this->title));
        }
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function setDateFromString(?string $date): self
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        $date = $date instanceof DateTimeInterface ? $date : null;
        return $this->setDate($date);
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): Article
    {
        $this->actif = $actif;
        return $this;
    }

}
