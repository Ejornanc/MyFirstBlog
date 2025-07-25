<?php

namespace App\Controllers;

class ErrorController extends ParentController
{
    public function error404()
    {
        // Afficher un log pour tester
        error_log("Erreur 404 déclenchée");
        $this->render('Error/error404');
    }
}
