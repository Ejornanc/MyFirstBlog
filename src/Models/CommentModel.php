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
     * Récupère les commentaires d'un article.
     * @return Comment[]
     */
    public function getCommentsByArticleId(int $articleId, bool $onlyApproved = true): array
    {
        $sql = "SELECT c.*, u.username FROM comments c INNER JOIN user u ON u.id = c.user_id WHERE c.article_id = :article_id";
        if ($onlyApproved) {
            $sql .= " AND c.is_approved = 1";
        }
        $sql .= " ORDER BY c.created_at DESC";

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
     * Compte les commentaires pour un article.
     */
    public function countCommentsByArticleId(int $articleId, bool $onlyApproved = true): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM comments WHERE article_id = :article_id";
        if ($onlyApproved) {
            $sql .= " AND is_approved = 1";
        }
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
