<?php

namespace App\Controllers;

use App\Entity\Article;
use App\Middleware\AuthMiddleware;
use App\Models\ArticleModel;

class AdminController extends ParentController
{
    private ArticleModel $articleModel;
    
    public function __construct()
    {
        $this->articleModel = new ArticleModel();
    }
    
    public function dashboard()
    {
        // Require admin access
        AuthMiddleware::requireAdmin();
        
        $articles = $this->articleModel->getAllArticles();
        
        $this->render('admin/dashboard', [
            'user' => AuthMiddleware::getUser(),
            'articles' => $articles,
        ]);
    }
    
    public function addArticle()
    {
        // Require admin access
        AuthMiddleware::requireAdmin();
        
        $errors = [];
        $success = false;
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $chapo = $_POST['chapo'] ?? '';
            $content = $_POST['content'] ?? '';
            $author = $_POST['author'] ?? '';
            
            // Validate inputs
            if (empty($title)) {
                $errors[] = 'Title is required';
            }
            
            if (empty($chapo)) {
                $errors[] = 'Chapo is required';
            }
            
            if (empty($content)) {
                $errors[] = 'Content is required';
            }
            
            if (empty($author)) {
                $errors[] = 'Author is required';
            }
            
            // If no errors, create the article
            if (empty($errors)) {
                $article = new Article();
                $article->setTitle($title)
                    ->setChapo($chapo)
                    ->setContent($content)
                    ->setAuthor($author)
                    ->setActif(true);
                
                if ($this->articleModel->createArticle($article)) {
                    $success = true;
                } else {
                    $errors[] = 'Failed to create article';
                }
            }
        }
        
        $this->render('admin/add_article', [
            'user' => AuthMiddleware::getUser(),
            'errors' => $errors,
            'success' => $success,
        ]);
    }
    
    public function editArticle($id)
    {
        // Require admin access
        AuthMiddleware::requireAdmin();
        
        $article = $this->articleModel->getArticle($id);
        $errors = [];
        $success = false;
        
        if (!$article) {
            header('Location: /admin/dashboard');
            exit;
        }
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $chapo = $_POST['chapo'] ?? '';
            $content = $_POST['content'] ?? '';
            $author = $_POST['author'] ?? '';
            
            // Validate inputs
            if (empty($title)) {
                $errors[] = 'Title is required';
            }
            
            if (empty($chapo)) {
                $errors[] = 'Chapo is required';
            }
            
            if (empty($content)) {
                $errors[] = 'Content is required';
            }
            
            if (empty($author)) {
                $errors[] = 'Author is required';
            }
            
            // If no errors, update the article
            if (empty($errors)) {
                $article->setTitle($title)
                    ->setChapo($chapo)
                    ->setContent($content)
                    ->setAuthor($author);
                
                if ($this->articleModel->updateArticle($article)) {
                    $success = true;
                } else {
                    $errors[] = 'Failed to update article';
                }
            }
        }
        
        $this->render('admin/edit_article', [
            'user' => AuthMiddleware::getUser(),
            'article' => $article,
            'errors' => $errors,
            'success' => $success,
        ]);
    }
    
    public function deleteArticle($id)
    {
        // Require admin access
        AuthMiddleware::requireAdmin();
        
        $article = $this->articleModel->getArticle($id);
        
        if (!$article) {
            header('Location: /admin/dashboard');
            exit;
        }
        
        // Process deletion
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->articleModel->deleteArticle($id)) {
                header('Location: /admin/dashboard?deleted=1');
                exit;
            } else {
                header('Location: /admin/dashboard?error=1');
                exit;
            }
        }
        
        $this->render('admin/delete_article', [
            'user' => AuthMiddleware::getUser(),
            'article' => $article,
        ]);
    }
}