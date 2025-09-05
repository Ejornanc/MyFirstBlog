<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Service\Mailer;

// PHPMailer wrapper

class HomeController extends ParentController
{
    public function home()
    {
        $user = AuthMiddleware::getUser();

        // Personal information to display on the homepage
        $personalInfo = [
            'name' => 'Alexandre Delcroix',
            'firstName' => 'Alexandre',
            'lastName' => 'Delcroix',
            'tagline' => 'Alexandre Delcroix, Symfony inside, bugs outside.',
            'description'  => 'Développeur PHP/Symfony en fin d’année, je transforme des besoins métiers en solutions web claires et maintenables. J’aime structurer des APIs robustes, modéliser les données (MySQL), et livrer vite sans sacrifier la qualité (tests, revues, outillage). J’utilise au quotidien Symfony, Twig, Docker, Bash, et Python pour automatiser et fiabiliser les workflows. Curieux, rigoureux et orienté produit, je cherche à rejoindre une équipe où je pourrai continuer à apprendre et à contribuer concrètement.',
            'cvLink' => '/assets/cv.pdf',
            'socialLinks' => [
                'github' => 'https://github.com/Ejornanc',
                'linkedin' => 'https://www.linkedin.com/in/alexandre-delcroix-488709216/',
            ],
        ];

        // Retrieve flash messages for contact form (then clear them)
        $contactErrors = $_SESSION['contact_errors'] ?? [];
        $contactSuccess = $_SESSION['contact_success'] ?? false;
        $contactOld = $_SESSION['contact_old'] ?? ['name' => '', 'email' => '', 'message' => ''];
        unset($_SESSION['contact_errors'], $_SESSION['contact_success'], $_SESSION['contact_old']);

        $this->render('home', [
            'user' => $user,
            'personalInfo' => $personalInfo,
            // expose to included contact form
            'errors' => $contactErrors,
            'success' => $contactSuccess,
            'old' => $contactOld,
        ]);
    }

    public function contact()
    {
        // Only accept POST; redirect to home otherwise
        if (!$this->isPost()) {
            header('Location: /#contact');
            exit;
        }

        $errors = [];
        $success = false;

        // CSRF validation
        $token = $_POST['csrf_token'] ?? null;
        if (!\App\Security\Csrf::validate($token)) {
            $errors[] = 'Invalid CSRF token';
        }

        // Process form submission
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Validate inputs
        if ($name === '') {
            $errors[] = 'Name is required';
        }

        if ($email === '') {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if ($message === '') {
            $errors[] = 'Message is required';
        }

        if (count($errors) === 0) {
            // Send email via MailHog using PHPMailer
            $mailer = new Mailer();
            $subject = 'Nouveau message de contact';
            $body = "<p><strong>Nom:</strong> " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</p>"
                  . "<p><strong>Email:</strong> " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</p>"
                  . "<p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</p>";

            $sendResult = $mailer->send('contact@mon-site.test', $subject, $body);
            if ($sendResult === true) {
                $success = true;
            } else {
                $errors[] = is_string($sendResult) ? $sendResult : 'Une erreur inconnue est survenue lors de l\'envoi de l\'email.';
                $success = false;
            }
        }

        // Store flash data in session and redirect to home (#contact)
        $_SESSION['contact_success'] = $success;
        $_SESSION['contact_errors'] = $errors;
        $_SESSION['contact_old'] = [
            'name' => $name,
            'email' => $email,
            'message' => $message,
        ];

        header('Location: /#contact');
        exit;
    }
}
