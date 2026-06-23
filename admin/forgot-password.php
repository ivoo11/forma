<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE email = :email
        AND activo = 1
        LIMIT 1
    ");

    $stmt->execute([
        'email' => $email
    ]);

    $user = $stmt->fetch();

    if (!$user) {

        $success = true;

    } else {

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $update = $pdo->prepare("
            UPDATE users
            SET
                password_reset_token = :token,
                password_reset_expires = :expires
            WHERE id = :id
        ");

        $update->execute([
            'token' => $token,
            'expires' => $expires,
            'id' => $user['id']
        ]);

        $resetUrl = 'https://somosforma.com.ar/admin/reset-password.php?token=' . $token;

        $mailSent = sendPasswordResetEmail(
            $user['email'],
            $user['nombre'],
            $resetUrl
        );

        if ($mailSent) {
            $success = true;
        } else {
            $error = 'No pudimos enviar el email de recuperación. Intentá nuevamente más tarde.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña | FORMA CMS</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>

<body class="admin-login">

<div class="login-box">

    <h1>Recuperar contraseña</h1>

    <?php if ($error): ?>
        <p class="admin-error">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <?php if ($success): ?>

        <div class="admin-success">
            <p>
                Si existe una cuenta activa con ese email, enviamos un enlace para restablecer la contraseña.
            </p>
        </div>

        <p style="margin-top:20px;">
            <a href="login.php">Volver al login</a>
        </p>

    <?php else: ?>

        <form method="POST">

            <input
                type="email"
                name="email"
                placeholder="Tu email"
                autocomplete="email"
                required
            >

            <button type="submit">
                Enviar enlace
            </button>

        </form>

        <p style="margin-top:20px;">
            <a href="login.php">Volver al login</a>
        </p>

    <?php endif; ?>

</div>

</body>
</html>