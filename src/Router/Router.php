<?php

declare(strict_types=1); // Active le typage strict

namespace App\Router;

use App\Controllers\AdminController;
use App\Controllers\BlogController;
use App\Controllers\ErrorController;
use App\Controllers\HomeController;
use App\Controllers\UserController;

class Router
{
    public static function defineRoutes($router)
    {
         // Initialize controllers
        $blogController = new BlogController();
        $homeController = new HomeController();
        $userController = new UserController();
        $adminController = new AdminController();
        $errorController = new ErrorController();

        // Start session for authentication
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Public routes
        $router->map('GET', '/', [$homeController, 'home']);
        $router->map('GET', '/error404', [$errorController, 'error404']);
        $router->map('GET', '/error', [$errorController, 'error404']);
        
        // Blog routes
        $router->map('GET', '/articles', [$blogController, 'articles']);
        $router->map('GET', '/article/[*:slug]-[i:id]', [$blogController, 'article'], 'article');
        $router->map('POST', '/article/[*:slug]-[i:id]', [$blogController, 'article']);
        
        // Contact route
        $router->map('POST', '/contact', [$homeController, 'contact']);
        
        // User authentication routes
        $router->map('GET', '/register', [$userController, 'register']);
        $router->map('POST', '/register', [$userController, 'register']);
        $router->map('GET', '/login', [$userController, 'login']);
        $router->map('POST', '/login', [$userController, 'login']);
        $router->map('GET', '/logout', [$userController, 'logout']);
        $router->map('GET', '/profile', [$userController, 'profile']);
        $router->map('POST', '/profile', [$userController, 'profile']);
        
        // Admin routes
        $router->map('GET', '/admin', [$adminController, 'dashboard']);
        $router->map('GET', '/admin/dashboard', [$adminController, 'dashboard']);
        $router->map('GET', '/admin/article/add', [$adminController, 'addArticle']);
        $router->map('POST', '/admin/article/add', [$adminController, 'addArticle']);
        $router->map('GET', '/admin/article/edit/[i:id]', [$adminController, 'editArticle']);
        $router->map('POST', '/admin/article/edit/[i:id]', [$adminController, 'editArticle']);
        $router->map('GET', '/admin/article/delete/[i:id]', [$adminController, 'deleteArticle']);
        $router->map('POST', '/admin/article/delete/[i:id]', [$adminController, 'deleteArticle']);
    }
}
