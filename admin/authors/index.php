<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$stmt = $pdo->query("
    SELECT *
    FROM authors
    ORDER BY fecha_creacion DESC
");

$authors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autores | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px;">
            <h1 class="admin-title" style="margin-bottom:0;">Autores</h1>
            <a href="create.php" class="admin-btn">Nuevo autor</a>
        </div>

        <div class="admin-card">

            <?php if (empty($authors)): ?>
                <p class="admin-muted">Todavía no hay autores cargados.</p>
            <?php else: ?>

                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="text-align:left; color:rgba(255,255,255,.55);">
                            <th style="padding:12px;">Nombre</th>
                            <th style="padding:12px;">Tipo</th>
                            <th style="padding:12px;">Institución / Universidad</th>
                            <th style="padding:12px;">Estado</th>
                            <th style="padding:12px;">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($authors as $author): ?>
                            <tr style="border-top:1px solid rgba(255,255,255,.12);">
                                <td style="padding:12px;">
                                    <?= htmlspecialchars($author['nombre'] . ' ' . $author['apellido']) ?>
                                </td>

                                <td style="padding:12px;">
                                    <?= htmlspecialchars($author['tipo_autor']) ?>
                                </td>

                                <td style="padding:12px;">
                                    <?php
                                        if ($author['tipo_autor'] === 'nueva_voz') {
                                            echo htmlspecialchars($author['universidad'] ?? '-');
                                        } else {
                                            echo htmlspecialchars($author['institucion'] ?? '-');
                                        }
                                    ?>
                                </td>

                                <td style="padding:12px;">
                                    <?= $author['activo'] ? 'Activo' : 'Inactivo' ?>
                                </td>

                                <td style="padding:12px;">
                                    <a href="edit.php?id=<?= $author['id'] ?>">Editar</a>
                                    |
                                    <a href="delete.php?id=<?= $author['id'] ?>" onclick="return confirm('¿Desactivar este autor?')">Desactivar</a>
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