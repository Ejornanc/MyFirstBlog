<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,          // Respect des règles PSR-12
        'array_syntax' => ['syntax' => 'short'], // [] au lieu de array()
        'single_quote' => true,    // Utiliser ' au lieu de "
        'no_unused_imports' => true, // Supprimer les "use" inutilisés
    ])
    ->setFinder($finder);
