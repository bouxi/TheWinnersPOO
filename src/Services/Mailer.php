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

        // 🧪 Configuration Mailtrap
        $this->mail->isSMTP();
        $this->mail->Host = 'sandbox.smtp.mailtrap.io';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $_ENV['MAIL_USERNAME'];
        $this->mail->Password = $_ENV['MAIL_PASSWORD'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;

        $this->mail->setFrom('no-reply@thewinnersguilde.fr', 'TheWinners');
        $this->mail->isHTML(true);

        // ✅ Correction d'encodage
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
