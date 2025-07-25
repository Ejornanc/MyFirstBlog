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
        $query = $this->pdo->prepare("SELECT * FROM article WHERE id = :id");
        $query->execute([
            "id" => $id,
        ]);
        $query->setFetchMode(PDO::FETCH_CLASS, Article::class);
        $article = $query->fetch();

        return $article ?: null;
    }

    public function getAllArticles(){
        $query = $this->pdo->prepare("SELECT * FROM article");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS, Article::class);
        $articles = $query->fetchAll();  // Utilisation de fetchAll() pour récupérer tous les articles

        return $articles ?: [];  // Retourne un tableau vide si aucun article n'est trouvé
    }

}