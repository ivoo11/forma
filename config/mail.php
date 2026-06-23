<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendPasswordResetEmail(
    string $toEmail,
    string $toName,
    string $resetUrl
): bool {

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();

        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;

        $mail->Username = 'hola@somosforma.com.ar';
        $mail->Password = 'Sanmontano2020$';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->CharSet = 'UTF-8';

        $mail->setFrom(
            'hola@somosforma.com.ar',
            'FORMA'
        );

        $mail->addAddress(
            $toEmail,
            $toName
        );

        $mail->isHTML(true);

        $mail->Subject = 'Recuperación de contraseña - FORMA CMS';

        $mail->Body = '
            <h2>Recuperación de contraseña</h2>

            <p>Recibimos una solicitud para restablecer tu contraseña.</p>

            <p>
                <a href="' . $resetUrl . '">
                    Restablecer contraseña
                </a>
            </p>

            <p>
                Este enlace expirará en 1 hora.
            </p>
        ';

        $mail->send();

        return true;

    } catch (Exception $e) {

        return false;

    }
}