<?php

require '../vendor/autoload.php';

use App\Controllers\ErrorController;
use App\Router\Router;

// Initialisation du routeur
$router = new AltoRouter();

// Définir les routes
Router::defineRoutes($router);

// Vérifier si une route correspond
$match = $router->match();

if ($match && is_callable($match['target'])) {
    // Si la route est valide et exécutable, on appelle la cible avec les paramètres
    call_user_func_array($match['target'], $match['params']);
} else {
    // Si aucune route ne correspond ou si la route est mal définie, afficher l'erreur 404
    header("HTTP/1.0 404 Not Found");
    call_user_func_array([new ErrorController(), 'error404'], []);
}
