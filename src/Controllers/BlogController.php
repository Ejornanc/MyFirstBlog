<?php

namespace App\Controllers;
use App\Entity\Comment;
use App\Models\ArticleModel;
use App\Models\CommentModel;

class BlogController extends ParentController
{
    private ArticleModel $articleModel;
    private CommentModel $commentModel;
    
    public function __construct()
    {
        $this->articleModel = new ArticleModel();
        $this->commentModel = new CommentModel();
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
        
        // Handle comment submission (requires logged-in user due to schema user_id NOT NULL)
        if ($this->isPost()) {
            // CSRF check
            $token = $_POST['csrf_token'] ?? null;
            if (!\App\Security\Csrf::validate($token)) {
                $errors[] = 'Invalid CSRF token';
            }

            /** @var Comment $comment */
            $comment = $this->hydrateFromForm (Comment::class);
            
            if (empty($comment->getContent())) {
                $errors[] = 'Comment is required';
            }
            
            // If no errors, attempt to save comment only if user is logged in
            $user = \App\Middleware\AuthMiddleware::getUser();
            if (empty($errors)) {
                if ($user && isset($user['id'])) {
                    $comment->setArticleId((int)$id)
                        ->setUserId((int)$user['id'])
                        ->setIsApproved(false);
                    try {
                        $this->commentModel->addComment($comment);
                        $commentSuccess = true;
                    } catch (\Throwable $e) {
                        $errors[] = 'Une erreur est survenue lors de l\'envoi du commentaire.';
                    }
                } else {
                    $errors[] = 'Vous devez être connecté pour poster un commentaire.';
                }
            }
        }

        // Fetch approved comments for this article
        $comments = $this->commentModel->getCommentsByArticleId((int)$id, true);

        $this->render('blog/article', [
            'article' => $article,
            'user' => \App\Middleware\AuthMiddleware::getUser(),
            'errors' => $errors,
            'commentSuccess' => $commentSuccess,
            'comments' => $comments,
        ]);
    }
}
