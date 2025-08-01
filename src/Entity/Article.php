<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;

class Article
{
    private ?int $id = null;
    private ?string $title = null;
    private ?string $content = null;
    private ?DateTimeInterface $dateObject = null;
    private ?string $slug = null;

    public function getId(): ?int
    {
        return $this->id;
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
        if (!$this->dateObject && $this->date) {
            $this->dateObject = DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
        }
        return $this->dateObject;
    }

    public function getSlug(): ?string
    {
        if (!$this->slug) {
            // Generate a slug from the title if not set
            $this->slug = strtolower(str_replace(' ', '-', $this->title));
        }
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function setDate(?string $date): self
    {
        if ($date) {
            $this->dateObject = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        }
        return $this;
    }
}
