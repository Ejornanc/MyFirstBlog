<?php

require '../vendor/autoload.php';

use App\Controllers\ErrorController;
use App\Router\Router;

$root = dirname(__DIR__);

if (file_exists($root.'/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($root);
    $dotenv->safeLoad(); // ne jette pas d’erreur si manquant
}

// Session cookie hardening (must be set before session_start in Router)
ini_set('session.cookie_httponly', '1');
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $secure ? true : false,
    'httponly' => true,
    'samesite' => 'Lax',
]);

// Security headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
// Conservative CSP; allow inline styles due to some inline style attributes in templates
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self';");

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
