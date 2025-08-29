<?php

namespace App\Controllers;

use App\Entity\Article;
use App\Middleware\AuthMiddleware;
use App\Models\ArticleModel;
use App\Models\CommentModel;

class AdminController extends ParentController
{
    private ArticleModel $articleModel;
    private CommentModel $commentModel;
    
    public function __construct()
    {
        $this->articleModel = new ArticleModel();
        $this->commentModel = new CommentModel();
    }
    
    public function dashboard()
    {
        // Require admin access
        AuthMiddleware::requireAdmin();
        
        $articles = $this->articleModel->getAllArticles();
        // Fetch unapproved comments for moderation (global list, still shown below)
        $allComments = $this->commentModel->getAllComments(false);
        $pendingComments = array_filter($allComments, fn($c) => !$c->isApproved());

        // Build per-article pending counts
        $pendingCounts = [];
        foreach ($articles as $a) {
            $pendingCounts[$a->getId()] = $this->commentModel->countPendingCommentsByArticleId((int)$a->getId());
        }
        
        $this->render('admin/dashboard', [
            'user' => AuthMiddleware::getUser(),
            'articles' => $articles,
            'pendingComments' => $pendingComments,
            'pendingCounts' => $pendingCounts,
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
            
            // If no errors, create the article
            if (empty($errors)) {
                $article = new Article();
                $article->setTitle($title)
                    ->setChapo($chapo)
                    ->setContent($content)
                    ->setActif(true);
                
                $currentUser = AuthMiddleware::getUser();
                $userId = (int)($currentUser['id'] ?? 0);
                if ($userId <= 0) {
                    $errors[] = 'Utilisateur non authentifié';
                } else {
                    if ($this->articleModel->createArticle($article, $userId)) {
                        $success = true;
                    } else {
                        $errors[] = 'Failed to create article';
                    }
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
            
            // If no errors, update the article
            if (empty($errors)) {
                $article->setTitle($title)
                    ->setChapo($chapo)
                    ->setContent($content);
                
                if ($this->articleModel->updateArticle($article)) {
                    $slug = $article->getSlug();
                    $id = $article->getId();
                    header('Location: /article/' . $slug . '-' . $id);
                    exit;
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

    public function approveComment($id)
    {
        // Require admin access
        AuthMiddleware::requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = $this->commentModel->approveComment((int)$id);
            // Optional redirect back to a specific page
            $redirect = $_GET['redirect'] ?? null;
            if ($redirect) {
                // Preserve a success flag if needed
                $sep = (str_contains($redirect, '?')) ? '&' : '?';
                $redirect = $redirect . $sep . ($success ? 'commentApproved=1' : 'commentError=1');
            } else {
                $redirect = '/admin/dashboard' . ($success ? '?commentApproved=1' : '?commentError=1');
            }
            header('Location: ' . $redirect);
            exit;
        }
        // If not POST, redirect back
        header('Location: /admin/dashboard');
        exit;
    }

    public function articleComments($id)
    {
        // Require admin access
        AuthMiddleware::requireAdmin();

        $article = $this->articleModel->getArticle((int)$id);
        if (!$article) {
            header('Location: /admin/dashboard');
            exit;
        }
        // Get pending comments only for this article
        $allForArticle = $this->commentModel->getCommentsByArticleId((int)$id, false);
        $pendingForArticle = array_filter($allForArticle, fn($c) => !$c->isApproved());

        $this->render('admin/article_comments', [
            'user' => AuthMiddleware::getUser(),
            'article' => $article,
            'pendingComments' => $pendingForArticle,
        ]);
    }

    public function rejectComment($id)
    {
        // Require admin access
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = $this->commentModel->rejectComment((int)$id);
            $redirect = $_GET['redirect'] ?? null;
            if ($redirect) {
                $sep = (str_contains($redirect, '?')) ? '&' : '?';
                $redirect = $redirect . $sep . ($success ? 'commentRejected=1' : 'commentError=1');
            } else {
                $redirect = '/admin/dashboard' . ($success ? '?commentRejected=1' : '?commentError=1');
            }
            header('Location: ' . $redirect);
            exit;
        }
        header('Location: /admin/dashboard');
        exit;
    }

    public function deleteComment($id)
    {
        // Require admin access
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = $this->commentModel->deleteComment((int)$id);
            $redirect = $_GET['redirect'] ?? null;
            if ($redirect) {
                $sep = (str_contains($redirect, '?')) ? '&' : '?';
                $redirect = $redirect . $sep . ($success ? 'commentDeleted=1' : 'commentError=1');
            } else {
                $redirect = '/admin/dashboard' . ($success ? '?commentDeleted=1' : '?commentError=1');
            }
            header('Location: ' . $redirect);
            exit;
        }
        header('Location: /admin/dashboard');
        exit;
    }
}