<?php

namespace App\Controllers;

use App\Security\Csrf;
use DateTimeInterface;
use Twig\TwigFunction;

abstract class ParentController
{
    protected function render(string $view, array $params = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader(realpath(__DIR__ . '/../../templates'));
        $twig = new \Twig\Environment($loader, [
            'autoescape' => 'html',
            // In production you may enable cache, debug=false
        ]);

        // Expose csrf token and a helper to render the hidden field
        $twig->addGlobal('csrf_token', Csrf::getToken());
        $twig->addFunction(new TwigFunction('csrf_field', function (): string {
            $token = Csrf::getToken();
            return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
        }, ['is_safe' => ['html']]));

        echo $twig->render($view . '.html.twig', $params);
    }

    protected function hydrateFromForm(string $entityClass)
    {
        $entity = new $entityClass();
        $formData = $_POST ?? [];
        foreach ($formData as $field => $value) {
            $setter = 'set' . ucfirst($field);
            if (method_exists($entity, $setter)) {
                $entity->$setter($value);
            }
        }
        return $entity;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
