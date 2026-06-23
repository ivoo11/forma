<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../partials/admin-only.php';

$error = '';

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = :id
    LIMIT 1
");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: index.php');
    exit;
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
            $stmt = $pdo->prepare("
                UPDATE users
                SET
                    nombre = :nombre,
                    email = :email,
                    rol = :rol,
                    activo = :activo
                WHERE id = :id
            ");

            $stmt->execute([
                'nombre' => $nombre,
                'email' => $email,
                'rol' => $rol,
                'activo' => $activo,
                'id' => $id
            ]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            $error = 'No se pudo actualizar el usuario. Puede que el email ya exista.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <h1 class="admin-title">Editar usuario</h1>

        <div class="admin-card">

            <?php if ($error): ?>
                <p class="admin-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST">

                <label>Nombre</label>
                <input type="text" name="nombre" required value="<?= htmlspecialchars($user['nombre']) ?>">

                <label>Email</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">

                <label>Rol</label>
                <select name="rol">
                    <option value="editor" <?= $user['rol'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                    <option value="admin" <?= $user['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>

                <label style="display:flex; gap:10px; align-items:center; margin-bottom:20px;">
                    <input type="checkbox" name="activo" <?= (int)$user['activo'] === 1 ? 'checked' : '' ?> style="width:auto; margin:0;">
                    Usuario activo
                </label>

                <button type="submit">Guardar cambios</button>

                <a href="/" class="admin-btn" style="background:#333; margin-left:10px;">
                    Cancelar
                </a>

            </form>

        </div>

    </main>

</div>

</body>
</html>