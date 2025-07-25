<?php

namespace App\Controllers;

abstract class ParentController
{
    protected function render(string $view, array $params = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader(realpath(__DIR__ . '/../../templates'));
        $twig = new \Twig\Environment($loader, [
        ]);

        echo $twig->render($view.'.html.twig', $params);
    }
}
