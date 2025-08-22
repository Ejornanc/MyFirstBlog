<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;

class Comment
{
    private ?int $id = null;
    private ?int $articleId = null;
    private ?int $userId = null;
    private ?string $author = null; // display name (from user.username)
    private ?string $content = null;
    private ?DateTimeInterface $date = null; // maps to created_at in DB
    private ?bool $isApproved = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getArticleId(): ?int
    {
        return $this->articleId;
    }

    public function setArticleId(?int $articleId): static
    {
        $this->articleId = $articleId;
        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): static
    {
        $this->userId = $userId;
        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): static
    {
        $this->author = $author;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): static
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

    public function isApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(?bool $isApproved): static
    {
        $this->isApproved = $isApproved;
        return $this;
    }
}
