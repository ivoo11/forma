<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$stats = [
    'articles' => $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
    'published' => $pdo->query("SELECT COUNT(*) FROM articles WHERE publicado = 1 AND activo = 1")->fetchColumn(),
    'drafts' => $pdo->query("SELECT COUNT(*) FROM articles WHERE publicado = 0 AND activo = 1")->fetchColumn(),
    'authors' => $pdo->query("SELECT COUNT(*) FROM authors WHERE activo = 1")->fetchColumn(),
    'questions' => $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users WHERE activo = 1")->fetchColumn(),
];

$latestArticles = $pdo->query("
    SELECT 
        a.id,
        a.titulo,
        a.slug,
        a.publicado,
        a.activo,
        c.nombre AS category_nombre
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    ORDER BY a.fecha_publicacion DESC, a.id DESC
    LIMIT 4
")->fetchAll();

$activeQuestionStmt = $pdo->query("
    SELECT 
        q.*,
        COUNT(qa.id) AS total_respuestas
    FROM questions q
    LEFT JOIN question_answers qa ON qa.question_id = q.id
    WHERE q.activa = 1
    GROUP BY q.id
    LIMIT 1
");

$activeQuestion = $activeQuestionStmt->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | FORMA CMS</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

    <main class="admin-main">

        <section class="dashboard-hero">
            <div>
                <span>FORMA CMS</span>
                <h1>Centro editorial</h1>
                <p>Gestión de contenidos, autores, preguntas y publicación.</p>
            </div>

            <a href="../index.php" target="_blank" class="dashboard-site-link">
                Ver sitio →
            </a>
        </section>

        <section class="dashboard-stats-row">

            <div class="dashboard-stat-card is-primary">
                <span>Artículos</span>
                <strong><?= (int)$stats['articles'] ?></strong>
            </div>

            <div class="dashboard-stat-card">
                <span>Publicados</span>
                <strong><?= (int)$stats['published'] ?></strong>
            </div>

            <div class="dashboard-stat-card">
                <span>Borradores</span>
                <strong><?= (int)$stats['drafts'] ?></strong>
            </div>

            <div class="dashboard-stat-card">
                <span>Autores</span>
                <strong><?= (int)$stats['authors'] ?></strong>
            </div>

            <div class="dashboard-stat-card">
                <span>Preguntas</span>
                <strong><?= (int)$stats['questions'] ?></strong>
            </div>

            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <div class="dashboard-stat-card">
                    <span>Usuarios</span>
                    <strong><?= (int)$stats['users'] ?></strong>
                </div>
            <?php endif; ?>

        </section>

        <section class="dashboard-layout">

            <div class="dashboard-main-column">

                <div class="dashboard-panel">
                    <div class="dashboard-panel-header">
                        <span>Accesos rápidos</span>
                        <h2>Crear contenido</h2>
                    </div>

                    <div class="dashboard-actions-grid">
                        <a href="articles/create.php" class="dashboard-action-card">
                            <span>+</span>
                            Nuevo artículo
                        </a>

                        <a href="authors/create.php" class="dashboard-action-card">
                            <span>+</span>
                            Nuevo autor
                        </a>

                        <a href="questions/create.php" class="dashboard-action-card">
                            <span>+</span>
                            Nueva pregunta
                        </a>

                        <a href="archivo.php" class="dashboard-action-card" target="_blank">
                            <span>↗</span>
                            Archivo público
                        </a>
                    </div>
                </div>

                <div class="dashboard-panel">
                    <div class="dashboard-panel-header">
                        <span>Actividad reciente</span>
                        <h2>Últimos artículos</h2>
                    </div>

                    <?php if (empty($latestArticles)): ?>

                        <p class="admin-muted">Todavía no hay artículos cargados.</p>

                    <?php else: ?>

                        <div class="dashboard-list">

                            <?php foreach ($latestArticles as $article): ?>

                                <div class="dashboard-list-item">

                                    <div>
                                        <strong><?= htmlspecialchars($article['titulo']) ?></strong>

                                        <p>
                                            <?= htmlspecialchars($article['category_nombre'] ?? 'Sin categoría') ?>
                                            · <?= (int)$article['publicado'] === 1 ? 'Publicado' : 'Borrador' ?>
                                            <?= (int)$article['activo'] === 0 ? ' · Oculto' : '' ?>
                                        </p>
                                    </div>

                                    <div class="dashboard-list-actions">
                                        <a href="articles/edit.php?id=<?= $article['id'] ?>">Editar</a>

                                        <?php if ((int)$article['publicado'] === 1 && (int)$article['activo'] === 1): ?>
                                            <a href="../articulo.php?slug=<?= urlencode($article['slug']) ?>" target="_blank">Ver</a>
                                        <?php endif; ?>
                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

            <aside class="dashboard-side-column">

                <div class="dashboard-panel dashboard-question-panel">
                    <div class="dashboard-panel-header">
                        <span>Home</span>
                        <h2>La Pregunta activa</h2>
                    </div>

                    <?php if ($activeQuestion): ?>

                        <strong>
                            <?= htmlspecialchars($activeQuestion['pregunta']) ?>
                        </strong>

                        <p>
                            <?= (int)$activeQuestion['total_respuestas'] ?>/4 perspectivas cargadas
                        </p>

                        <div class="dashboard-list-actions">
                            <a href="questions/answers.php?id=<?= $activeQuestion['id'] ?>">Editar</a>
                            <a href="../pregunta.php?slug=<?= urlencode($activeQuestion['slug']) ?>" target="_blank">Ver</a>
                        </div>

                    <?php else: ?>

                        <p class="admin-muted">No hay una pregunta activa.</p>

                    <?php endif; ?>
                </div>

                <div class="dashboard-panel">
                    <div class="dashboard-panel-header">
                        <span>Sesión</span>
                        <h2><?= htmlspecialchars($_SESSION['user_name']) ?></h2>
                    </div>

                    <p class="admin-muted">
                        Rol: <?= htmlspecialchars(ucfirst($_SESSION['user_role'])) ?>
                    </p>

                    <div class="dashboard-list-actions">
                        <a href="logout.php">Cerrar sesión</a>
                    </div>
                </div>

            </aside>

        </section>

    </main>

</div>

</body>
</html>