<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Services\Mailer;

$_ENV['MAILTRAP_USER'] = 'xxxxxxxxxxxxxx'; // ou charger avec ta classe Env
$_ENV['MAILTRAP_PASS'] = 'xxxxxxxxxxxxxx';

$result = Mailer::send('ton.email@mailtrap.io', 'Test PHPMailer', '<p>Mail fonctionnel 🎉</p>');

echo $result ? '✅ Envoyé !' : '❌ Échec.';
