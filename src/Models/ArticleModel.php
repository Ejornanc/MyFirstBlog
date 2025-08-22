<?php

namespace App\Models;

use App\Database\Database;
use App\Entity\Article;
use PDO;

class ArticleModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection(); // Récupère la connexion unique
    }

    public function getArticle(int $id): ?Article
    {
        $query = $this->pdo->prepare("SELECT a.*, u.email FROM article a LEFT JOIN user u ON a.user_id = u.id WHERE a.id = :id");
        $query->execute([
            "id" => $id,
        ]);
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $articleData = $query->fetch();
        //dd($articleData);
        if (!$articleData) {
            return null;
        }
        
        $article = new Article();
        $article->setId($articleData['id'])
            ->setTitle($articleData['title'])
            ->setChapo($articleData['chapo'])
            ->setContent($articleData['content'])
            ->setDateFromString($articleData['date']);
            
        // Set new fields if they exist in the database

        
        if (isset($articleData['author'])) {
            $article->setAuthor($articleData['author']);
        }

        return $article;
    }

    public function getAllArticles(){
        $query = $this->pdo->prepare("SELECT * FROM article ORDER BY date DESC");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $articlesDb = $query->fetchAll();  // Utilisation de fetchAll() pour récupérer tous les articles
        $articles = [];
        foreach ($articlesDb as $articleDb) {
            $article = new Article();
            $article->setId($articleDb['id'])
                ->setTitle($articleDb['title'])
                ->setContent($articleDb['content'])
                ->setDateFromString($articleDb['date']);
                
            // Set new fields if they exist in the database
            if (isset($articleDb['chapo'])) {
                $article->setChapo($articleDb['chapo']);
            }
            
            if (isset($articleDb['author'])) {
                $article->setAuthor($articleDb['author']);
            }
            
            if (isset($articleDb['updated_at'])) {
                $article->setUpdatedAtFromString($articleDb['updated_at']);
            }
            
            if (isset($articleDb['slug'])) {
                $article->setSlug($articleDb['slug']);
            }
            
            if (isset($articleDb['actif'])) {
                $article->setActif($articleDb['actif']);
            }
            
            $articles[] = $article;
        }

        return $articles;
    }
    
    public function createArticle(Article $article): bool
    {
        $query = $this->pdo->prepare("
            INSERT INTO article (title, chapo, content, user_id, date) 
            VALUES (:title, :chapo, :content, :user_id, :date)
        ");
        
        $now = new \DateTime();
        $dateStr = $now->format('Y-m-d H:i:s');
        
        return $query->execute([
            'title' => $article->getTitle(),
            'chapo' => $article->getChapo(),
            'content' => $article->getContent(),
            'user_id' => null,
            'date' => $dateStr,
        ]);
    }
    
    public function updateArticle(Article $article): bool
    {
        $query = $this->pdo->prepare("
            UPDATE article 
            SET title = :title, 
                chapo = :chapo, 
                content = :content
            WHERE id = :id
        ");
        
        return $query->execute([
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'chapo' => $article->getChapo(),
            'content' => $article->getContent(),
        ]);
    }
    
    public function deleteArticle(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM article WHERE id = :id");
        return $query->execute(['id' => $id]);
    }
}