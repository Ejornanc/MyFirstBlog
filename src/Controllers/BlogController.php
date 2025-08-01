<?php

namespace App\Controllers;
use App\Entity\Article;
use App\Models\ArticleModel;

class BlogController extends ParentController
{
    public function home(){
        $this->render('home', ["chef" => "<h1>c'est moi</h1>"]);
    }

    public function suite(){
        $this->render('suite');
    }
    public function demo(){
        $this->render('demo');
    }

    public function contact(){
        $this->render('contact');
    }

    public function article($slug, $id)
    {
        $model = new ArticleModel();
        $article = $model->getArticle($id); // Récupère l'article par ID
        if (!$article) {
            // Gérer le cas où l'article n'existe pas
            echo "Article non trouvé.";
            exit;
        }

        $this->render('Blog/Show_Blog', [
            'article' => $article, // Passer l'article à la vue
        ]);
    }

    public function articles(){
        $model = new ArticleModel();
        $articles = $model->getAllArticles();

        // Ajouter les URLs aux articles
        foreach ($articles as $article) {
            $article->url = "/Blog/" . $article->getSlug() . "-" . $article->getId(); // Crée l'URL pour chaque article
        }

        // Passer les articles avec l'URL à la vue
        $this->render('Blog/Index_Blog', [
            "articles" => $articles,
        ]);
    }


}
