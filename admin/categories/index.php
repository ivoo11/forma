<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$stmt = $pdo->query("
    SELECT *
    FROM categories
    ORDER BY orden ASC, nombre ASC
");

$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;">
            <h1 class="admin-title" style="margin-bottom:0;">
                Categorías
            </h1>

            <a href="create.php" class="admin-btn">
                Nueva categoría
            </a>
        </div>

        <div class="admin-card">

            <?php if (empty($categories)): ?>

                <p class="admin-muted">
                    No hay categorías cargadas.
                </p>

            <?php else: ?>

                <table style="width:100%;border-collapse:collapse;">

                    <thead>
                        <tr style="text-align:left;color:rgba(255,255,255,.55);">
                            <th style="padding:12px;">Nombre</th>
                            <th style="padding:12px;">Slug</th>
                            <th style="padding:12px;">Orden</th>
                            <th style="padding:12px;">Estado</th>
                            <th style="padding:12px;">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($categories as $category): ?>

                        <tr style="border-top:1px solid rgba(255,255,255,.12);">

                            <td style="padding:12px;">
                                <?= htmlspecialchars($category['nombre']) ?>
                            </td>

                            <td style="padding:12px;">
                                <?= htmlspecialchars($category['slug']) ?>
                            </td>

                            <td style="padding:12px;">
                                <?= htmlspecialchars($category['orden']) ?>
                            </td>

                            <td style="padding:12px;">
                                <?= $category['activa'] ? 'Activa' : 'Inactiva' ?>
                            </td>

                            <td style="padding:12px;">
                                <a href="edit.php?id=<?= $category['id'] ?>">
                                    Editar
                                </a>
                                |
                                <a href="delete.php?id=<?= $category['id'] ?>"
                                   onclick="return confirm('¿Desactivar esta categoría?')">
                                    Desactivar
                                </a>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>