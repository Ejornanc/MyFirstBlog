<?php

namespace App\Service;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        // Config de base pour MailHog (override via env if provided)
        $host = getenv('SMTP_HOST') ?: 'mailhog';
        $port = getenv('SMTP_PORT') ? (int) getenv('SMTP_PORT') : 1025;
        $fromEmail = getenv('MAIL_FROM_EMAIL') ?: 'no-reply@mon-site.test';
        $fromName = getenv('MAIL_FROM_NAME') ?: 'Mon Site';

        $this->mailer->isSMTP();
        $this->mailer->Host = $host;   // 'mailhog' si le site est dans Docker, sinon 'localhost'
        $this->mailer->Port = $port;
        $this->mailer->SMTPAuth = false;
        $this->mailer->SMTPAutoTLS = false; // ne force pas STARTTLS
        $this->mailer->SMTPSecure = false;
        $this->mailer->CharSet = 'UTF-8';

        $this->mailer->setFrom($fromEmail, $fromName);
    }

    public function send($to, $subject, $body)
    {
        try {
            $this->mailer->clearAllRecipients(); // reset si réutilisé
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->isHTML(true); // si tu veux envoyer du HTML

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            return "Erreur : {$this->mailer->ErrorInfo}";
        }
    }
}
