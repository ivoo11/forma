<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../partials/admin-only.php';

$users = $pdo->query("
    SELECT *
    FROM users
    ORDER BY activo DESC, rol ASC, nombre ASC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px;">
            <h1 class="admin-title" style="margin-bottom:0;">Usuarios</h1>
            <a href="create.php" class="admin-btn">Nuevo usuario</a>
        </div>

        <div class="admin-card">

            <?php if (empty($users)): ?>

                <p class="admin-muted">Todavía no hay usuarios cargados.</p>

            <?php else: ?>

                <div style="display:flex; flex-direction:column; gap:16px;">

                    <?php foreach ($users as $user): ?>

                        <div class="article-admin-card">

                            <strong><?= htmlspecialchars($user['nombre']) ?></strong>

                            <p class="admin-muted" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                <span><?= htmlspecialchars($user['email']) ?></span>
                                <span><?= htmlspecialchars($user['rol']) ?></span>

                                <?php if ((int)$user['activo'] === 1): ?>
                                    <span class="status-badge status-active">Activo</span>
                                <?php else: ?>
                                    <span class="status-badge status-inactive">Inactivo</span>
                                <?php endif; ?>

                                <?php if ((int)$user['debe_cambiar_password'] === 1): ?>
                                    <span class="status-badge status-warning">Debe cambiar clave</span>
                                <?php endif; ?>
                            </p>

                            <div class="article-admin-actions">
                                <a href="edit.php?id=<?= $user['id'] ?>">Editar</a>

                                <?php if ((int)$user['activo'] === 1): ?>
                                    <a
                                        href="user-status.php?id=<?= $user['id'] ?>&action=disable"
                                        onclick="return confirm('¿Desactivar este usuario?');"
                                    >
                                        Desactivar
                                    </a>
                                <?php else: ?>
                                    <a href="user-status.php?id=<?= $user['id'] ?>&action=enable">
                                        Activar
                                    </a>
                                <?php endif; ?>

                                <a
                                    href="force-password.php?id=<?= $user['id'] ?>"
                                    onclick="return confirm('¿Forzar cambio de contraseña para este usuario?');"
                                >
                                    Forzar cambio de clave
                                </a>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>