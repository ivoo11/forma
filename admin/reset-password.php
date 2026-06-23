<?php
require_once __DIR__ . '/../config/database.php';

$error = '';
$success = '';

$token = $_GET['token'] ?? '';

if ($token === '') {
    $error = 'El enlace de recuperación no es válido.';
    $user = null;
} else {
    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE password_reset_token = :token
        AND password_reset_expires > NOW()
        AND activo = 1
        LIMIT 1
    ");

    $stmt->execute([
        'token' => $token
    ]);

    $user = $stmt->fetch();

    if (!$user) {
        $error = 'El enlace es inválido o ya venció.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {

    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Las contraseñas no coinciden.';
    } else {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $update = $pdo->prepare("
            UPDATE users
            SET
                password_hash = :password_hash,
                password_reset_token = NULL,
                password_reset_expires = NULL,
                debe_cambiar_password = 0
            WHERE id = :id
        ");

        $update->execute([
            'password_hash' => $passwordHash,
            'id' => $user['id']
        ]);

        $success = 'Contraseña actualizada correctamente. Ya podés ingresar.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña | FORMA CMS</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>

<body class="admin-login">

<div class="login-box">

    <h1>Restablecer contraseña</h1>

    <?php if ($error): ?>
        <p class="admin-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>

        <div class="admin-success">
            <p><?= htmlspecialchars($success) ?></p>
            <a href="login.php">Volver al login</a>
        </div>

    <?php elseif ($user): ?>

        <form method="POST">

            <label>Nueva contraseña</label>
            <input
                type="password"
                name="password"
                required
                autocomplete="new-password"
            >

            <label>Repetir contraseña</label>
            <input
                type="password"
                name="confirm_password"
                required
                autocomplete="new-password"
            >

            <button type="submit">
                Guardar nueva contraseña
            </button>

        </form>

    <?php endif; ?>

</div>

</body>
</html>