<?php

namespace App\Controllers;

use DateTimeInterface;

abstract class ParentController
{
    protected function render(string $view, array $params = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader(realpath(__DIR__ . '/../../templates'));
        $twig = new \Twig\Environment($loader, [
        ]);

        echo $twig->render($view.'.html.twig', $params);
    }

    protected function hydrateFromForm(string $entityClass)
    {
        $entity = new $entityClass();
        $formData = $_POST ?? [];
        foreach ($formData as $field => $value) {
            $setter = 'set'.ucfirst($field);
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
