<?php

namespace App\Services;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // Configuration SMTP Amen.fr
        $this->mail->isSMTP();
        $this->mail->Host = $_ENV['MAILER_HOST'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $_ENV['MAILER_USER'];
        $this->mail->Password = $_ENV['MAILER_PASS'];

        // Utilisation SSL (port 465)
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // ⚙️ SSL obligatoire pour le port 465
        $this->mail->Port = $_ENV['MAILER_PORT'];              // 465

        $this->mail->setFrom($_ENV['MAILER_FROM'], $_ENV['MAILER_NAME']);
        $this->mail->isHTML(true);

        // Encodage UTF-8
        $this->mail->CharSet = 'UTF-8';
    }

    public function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $toName);
            $this->mail->Subject = $subject;
            $this->mail->Body = $htmlBody;

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Erreur envoi mail : " . $e->getMessage());
            return false;
        }
    }
}
