<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../partials/admin-only.php';

$error = '';
$tempPassword = '';

function generateTempPassword($length = 12)
{
    return substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@$%'), 0, $length);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = $_POST['rol'] ?? 'editor';
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($nombre === '' || $email === '') {
        $error = 'Nombre y email son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no es válido.';
    } elseif (!in_array($rol, ['admin', 'editor'])) {
        $error = 'Rol inválido.';
    } else {
        try {
            $tempPassword = generateTempPassword();
            $passwordHash = password_hash($tempPassword, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (
                    nombre,
                    email,
                    password_hash,
                    rol,
                    activo,
                    debe_cambiar_password
                ) VALUES (
                    :nombre,
                    :email,
                    :password_hash,
                    :rol,
                    :activo,
                    1
                )
            ");

            $stmt->execute([
                'nombre' => $nombre,
                'email' => $email,
                'password_hash' => $passwordHash,
                'rol' => $rol,
                'activo' => $activo
            ]);

        } catch (PDOException $e) {
            $error = 'No se pudo crear el usuario. Puede que el email ya exista.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo usuario | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <h1 class="admin-title">Nuevo usuario</h1>

        <div class="admin-card">

            <?php if ($error): ?>
                <p class="admin-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($tempPassword): ?>
                <div class="admin-success" style="margin-bottom:24px;">
                    <strong>Usuario creado.</strong><br>
                    Contraseña temporal:
                    <code style="font-size:18px; display:inline-block; margin-top:8px;">
                        <?= htmlspecialchars($tempPassword) ?>
                    </code>
                    <p class="admin-muted" style="margin-top:10px;">
                        El usuario deberá cambiarla en su primer ingreso.
                    </p>
                </div>

                <a href="/" class="admin-btn">Volver a usuarios</a>
                <a href="create.php" class="admin-btn" style="background:#333; margin-left:10px;">Crear otro</a>

            <?php else: ?>

                <form method="POST">

                    <label>Nombre</label>
                    <input type="text" name="nombre" required>

                    <label>Email</label>
                    <input type="email" name="email" required>

                    <label>Rol</label>
                    <select name="rol">
                        <option value="editor">Editor</option>
                        <option value="admin">Admin</option>
                    </select>

                    <label style="display:flex; gap:10px; align-items:center; margin-bottom:20px;">
                        <input type="checkbox" name="activo" checked style="width:auto; margin:0;">
                        Usuario activo
                    </label>

                    <button type="submit">Crear usuario</button>

                    <a href="/" class="admin-btn" style="background:#333; margin-left:10px;">
                        Cancelar
                    </a>

                </form>

            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>