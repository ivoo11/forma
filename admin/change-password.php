<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE users
            SET
                password_hash = :password_hash,
                debe_cambiar_password = 0
            WHERE id = :id
        ");

        $stmt->execute([
            'password_hash' => $passwordHash,
            'id' => $_SESSION['user_id']
        ]);

        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar contraseña | FORMA CMS</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>

<body>

<div class="admin-login">

    <div class="admin-login-card">

        <h1>Cambiar contraseña</h1>

        <p class="admin-muted" style="margin-bottom:20px;">
            Por seguridad, tenés que crear una nueva contraseña antes de ingresar al CMS.
        </p>

        <?php if ($error): ?>
            <p class="admin-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">

            <label>Nueva contraseña</label>
            <input type="password" name="password" required>

            <label>Repetir contraseña</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Guardar contraseña</button>

        </form>

    </div>

</div>

</body>
</html>