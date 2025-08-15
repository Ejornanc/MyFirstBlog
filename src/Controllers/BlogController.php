<?php

namespace App\Controllers;
use App\Models\ArticleModel;

class BlogController extends ParentController
{
    private ArticleModel $articleModel;
    
    public function __construct()
    {
        $this->articleModel = new ArticleModel();
    }
    
    public function articles()
    {
        $articles = $this->articleModel->getAllArticles();

        // Add URLs to articles
        foreach ($articles as $article) {
            $article->url = "/article/" . $article->getSlug() . "-" . $article->getId();
        }

        $this->render('blog/articles', [
            "articles" => $articles,
            "user" => \App\Middleware\AuthMiddleware::getUser(),
        ]);
    }

    public function article($slug, $id)
    {
        $article = $this->articleModel->getArticle($id);
        $errors = [];
        $commentSuccess = false;

        if (!$article) {
            header('Location: /error404');
            exit;
        }
        
        // Handle comment submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $author = $_POST['author'] ?? '';
            $content = $_POST['content'] ?? '';
            
            // Validate inputs
            if (empty($author)) {
                $errors[] = 'Name is required';
            }
            
            if (empty($content)) {
                $errors[] = 'Comment is required';
            }
            
            // If no errors, save the comment (to be implemented with CommentModel)
            if (empty($errors)) {
                // For now, just show success message
                // In a real implementation, we would save to database
                $commentSuccess = true;
            }
        }

        $this->render('blog/article', [
            'article' => $article,
            'user' => \App\Middleware\AuthMiddleware::getUser(),
            'errors' => $errors,
            'commentSuccess' => $commentSuccess,
            // In a real implementation, we would fetch comments from database
            'comments' => [],
        ]);
    }
}
