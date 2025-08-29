<?php

namespace App\Models;

use App\Database\Database;
use App\Entity\Comment;
use PDO;

class CommentModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Récupère un commentaire par son ID.
     */
    public function getComment(int $id): ?Comment
    {
        $sql = 'SELECT c.*, u.username FROM comments c INNER JOIN user u ON u.id = c.user_id WHERE c.id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return $this->mapRowToComment($row);
    }

    /**
     * Récupère tous les commentaires.
     * @return Comment[]
     */
    public function getAllComments(bool $onlyApproved = false): array
    {
        $sql = 'SELECT c.*, u.username FROM comments c INNER JOIN user u ON u.id = c.user_id';
        if ($onlyApproved) {
            $sql .= ' WHERE c.is_approved = 1';
        }
        $sql .= ' ORDER BY c.created_at DESC';
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map(fn ($row) => $this->mapRowToComment($row), $rows);
    }

    /**
     * Récupère les commentaires d'un article.
     * @return Comment[]
     */
    public function getCommentsByArticleId(int $articleId, bool $onlyApproved = true): array
    {
        $sql = 'SELECT c.*, u.username FROM comments c INNER JOIN user u ON u.id = c.user_id WHERE c.article_id = :article_id';
        if ($onlyApproved) {
            $sql .= ' AND c.is_approved = 1';
        }
        $sql .= ' ORDER BY c.created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['article_id' => $articleId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $comments = [];
        foreach ($rows as $row) {
            $comments[] = $this->mapRowToComment($row);
        }
        return $comments;
    }

    /**
     * Ajoute un commentaire (par défaut non approuvé).
     */
    public function addComment(Comment $comment): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO comments (article_id, user_id, content, created_at, is_approved)
            VALUES (:article_id, :user_id, :content, :created_at, :is_approved)
        ');

        $now = new \DateTime();
        $dateStr = $now->format('Y-m-d H:i:s');

        return $stmt->execute([
            'article_id' => $comment->getArticleId(),
            'user_id' => $comment->getUserId(),
            'content' => $comment->getContent(),
            'created_at' => $dateStr,
            'is_approved' => $comment->isApproved() ? 1 : 0,
        ]);
    }

    /**
     * Met à jour un commentaire.
     */
    public function updateComment(Comment $comment): bool
    {
        $stmt = $this->pdo->prepare('UPDATE comments SET content = :content, is_approved = :is_approved WHERE id = :id');
        return $stmt->execute([
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
            'is_approved' => $comment->isApproved() ? 1 : 0,
        ]);
    }

    /**
     * Approuve un commentaire par ID.
     */
    public function approveComment(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE comments SET is_approved = 1 WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Supprime un commentaire.
     */
    public function deleteComment(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM comments WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Invalide (rejette) un commentaire par ID (is_approved = 0).
     */
    public function rejectComment(int $id): bool
    {
        $stmt = $this->pdo->prepare('UPDATE comments SET is_approved = 0 WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Compte les commentaires pour un article.
     */
    public function countCommentsByArticleId(int $articleId, bool $onlyApproved = true): int
    {
        $sql = 'SELECT COUNT(*) as cnt FROM comments WHERE article_id = :article_id';
        if ($onlyApproved) {
            $sql .= ' AND is_approved = 1';
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['article_id' => $articleId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }

    /**
     * Compte les commentaires en attente (non approuvés) pour un article.
     */
    public function countPendingCommentsByArticleId(int $articleId): int
    {
        $sql = 'SELECT COUNT(*) as cnt FROM comments WHERE article_id = :article_id AND is_approved = 0';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['article_id' => $articleId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }

    private function mapRowToComment(array $row): Comment
    {
        $comment = new Comment();
        $comment->setId((int)$row['id'])
            ->setArticleId((int)$row['article_id'])
            ->setUserId((int)($row['user_id'] ?? 0) ?: null)
            ->setAuthor($row['username'] ?? null)
            ->setContent($row['content'] ?? null)
            ->setDateFromString($row['created_at'] ?? null)
            ->setIsApproved(isset($row['is_approved']) ? (bool)$row['is_approved'] : null);
        return $comment;
    }
}
