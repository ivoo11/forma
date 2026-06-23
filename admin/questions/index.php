<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$questions = $pdo->query("
    SELECT 
        q.*,
        COUNT(qa.id) AS total_respuestas
    FROM questions q
    LEFT JOIN question_answers qa ON qa.question_id = q.id
    GROUP BY q.id
    ORDER BY q.activa DESC, q.fecha_publicacion DESC, q.id DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>La Pregunta | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px;">
            <h1 class="admin-title" style="margin-bottom:0;">La Pregunta</h1>
            <a href="create.php" class="admin-btn">Nueva pregunta</a>
        </div>

        <div class="admin-card">

            <?php if (empty($questions)): ?>

                <p class="admin-muted">Todavía no hay preguntas cargadas.</p>

            <?php else: ?>

                <div style="display:flex; flex-direction:column; gap:16px;">

                    <?php foreach ($questions as $question): ?>

                        <div class="article-admin-card">

                            <strong><?= htmlspecialchars($question['pregunta']) ?></strong>

                            <p class="admin-muted" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                <span><?= (int)$question['total_respuestas'] ?>/4 respuestas</span>

                                <?php if ((int)$question['activa'] === 1): ?>
                                    <span class="status-badge status-active">Activa</span>
                                <?php else: ?>
                                    <span class="status-badge status-inactive">Inactiva</span>
                                <?php endif; ?>

                                <span>
                                    <?= !empty($question['fecha_publicacion']) ? date('d/m/Y', strtotime($question['fecha_publicacion'])) : '-' ?>
                                </span>
                            </p>

                            <?php if (!empty($question['bajada'])): ?>
                                <p class="admin-muted" style="margin-top:8px;">
                                    <?= htmlspecialchars($question['bajada']) ?>
                                </p>
                            <?php endif; ?>

                            <div class="article-admin-actions">

                                <a href="../../pregunta.php?slug=<?= urlencode($question['slug']) ?>" target="_blank">
                                    Ver
                                </a>

                                <a href="answers.php?id=<?= $question['id'] ?>">
                                    Respuestas
                                </a>

                                <a href="edit.php?id=<?= $question['id'] ?>">
                                    Editar
                                </a>

                                <?php if ((int)$question['activa'] !== 1): ?>
                                    <a
                                        href="question-status.php?id=<?= $question['id'] ?>"
                                        onclick="return confirm('¿Publicar esta pregunta en la home? Esto reemplazará la pregunta actualmente activa.');"
                                    >
                                        Publicar en home
                                    </a>
                                <?php endif; ?>

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