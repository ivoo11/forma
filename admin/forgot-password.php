<?php
require_once __DIR__ . '/../config/database.php';

$error = '';
$successLink = '';

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

        $error = 'No existe un usuario activo con ese email.';

    } else {

        $token = bin2hex(random_bytes(32));

        $expires = date(
            'Y-m-d H:i:s',
            strtotime('+1 hour')
        );

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

        $successLink =
            'reset-password.php?token=' .
            $token;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
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

    <?php if ($successLink): ?>

        <div class="admin-success">

            <p>
                Link generado:
            </p>

            <a href="<?= htmlspecialchars($successLink) ?>">
                <?= htmlspecialchars($successLink) ?>
            </a>

        </div>

    <?php else: ?>

        <form method="POST">

            <input
                type="email"
                name="email"
                placeholder="Tu email"
                required
            >

            <button type="submit">
                Generar enlace
            </button>

        </form>

    <?php endif; ?>

</div>

</body>
</html>