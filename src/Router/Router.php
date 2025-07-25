<?php

declare(strict_types=1); // Active le typage strict

namespace App\Router;

use App\Controllers\BlogController;
use App\Controllers\ErrorController;

class Router
{
    public static function defineRoutes($router)
    {
        $blogController = new BlogController();
        $errorController = new ErrorController();

        $router->map('GET', '/', [$blogController, 'home']);
        $router->map('GET', '/error404', [$errorController, 'error404']);
        $router->map('GET', '/suite', [$blogController, 'suite']);
        $router->map('GET', '/demo', [$blogController, 'demo']);
        $router->map('GET', '/contact', [$blogController, 'contact']);
        $router->map('GET', '/article/[*:slug]-[i:id]', [$blogController, 'article'], 'article');
        $router->map('GET', '/error', [$errorController, 'error404']);
        // $router->map('GET', '/article', [$blogController, 'article']);
        $router->map('GET', '/articles', [$blogController, 'articles']);
    }
}
