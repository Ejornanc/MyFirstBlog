<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;

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
        
        $this->render('home', [
            'user' => $user,
            'personalInfo' => $personalInfo,
        ]);
    }
    
    public function contact()
    {
        $user = AuthMiddleware::getUser();
        $errors = [];
        $success = false;
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';
            
            // Validate inputs
            if (empty($name)) {
                $errors[] = 'Name is required';
            }
            
            if (empty($email)) {
                $errors[] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }
            
            if (empty($message)) {
                $errors[] = 'Message is required';
            }
            
            // If no errors, send the email
            if (empty($errors)) {
                $to = 'your-email@example.com'; // Replace with your email
                $subject = 'Contact Form Submission from ' . $name;
                $emailBody = "Name: $name\n";
                $emailBody .= "Email: $email\n\n";
                $emailBody .= "Message:\n$message";
                $headers = "From: $email";
                
                if (mail($to, $subject, $emailBody, $headers)) {
                    $success = true;
                } else {
                    $errors[] = 'Failed to send email';
                }
            }
        }
        
        $this->render('contact', [
            'user' => $user,
            'errors' => $errors,
            'success' => $success,
        ]);
    }
}