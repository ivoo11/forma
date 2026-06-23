<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$categories = $pdo->query("
    SELECT *
    FROM categories
    WHERE activa = 1
    ORDER BY orden ASC, nombre ASC
")->fetchAll();

$articlesStmt = $pdo->query("
    SELECT
        articles.*,
        authors.nombre AS author_nombre,
        authors.apellido AS author_apellido,
        categories.nombre AS category_nombre,
        categories.slug AS category_slug
    FROM articles
    LEFT JOIN authors ON articles.author_id = authors.id
    LEFT JOIN categories ON articles.category_id = categories.id
    ORDER BY articles.fecha_publicacion DESC, articles.id DESC
");

$allArticles = $articlesStmt->fetchAll();

function articlesByCategory($articles, $categoryId) {
    return array_values(array_filter($articles, function($article) use ($categoryId) {
        return (int)$article['category_id'] === (int)$categoryId;
    }));
}

function publishedActive($articles) {
    return array_values(array_filter($articles, function($article) {
        return (int)$article['publicado'] === 1 && (int)$article['activo'] === 1;
    }));
}

function drafts($articles) {
    return array_values(array_filter($articles, function($article) {
        return (int)$article['publicado'] === 0 && (int)$article['activo'] === 1;
    }));
}

function hiddenArticles($articles) {
    return array_values(array_filter($articles, function($article) {
        return (int)$article['activo'] === 0;
    }));
}

function buildEditorialSections($articles) {
    $published = publishedActive($articles);

    $pinned = array_values(array_filter($published, function($article) {
        return (int)$article['anclado'] === 1;
    }));

    $automatic = array_values(array_filter($published, function($article) {
        return (int)$article['anclado'] !== 1;
    }));

    $sections = [
        'foco' => [],
        'ecos' => [],
        'trama' => [],
        'archivo' => []
    ];

    foreach ($pinned as $article) {
        $pos = $article['posicion_anclada'];
        if (isset($sections[$pos])) {
            $sections[$pos][] = $article;
        }
    }

    foreach (['foco', 'ecos', 'trama'] as $section) {
        usort($sections[$section], function($a, $b) {
            return ((int)$a['orden_anclado']) <=> ((int)$b['orden_anclado']);
        });
    }

    $limits = [
        'foco' => 1,
        'ecos' => 2,
        'trama' => 6
    ];

    foreach (['foco', 'ecos', 'trama'] as $section) {
        while (count($sections[$section]) < $limits[$section] && !empty($automatic)) {
            $sections[$section][] = array_shift($automatic);
        }
    }

    $sections['archivo'] = $automatic;

    return $sections;
}

function buildNuevasVocesSections($articles) {
    $published = publishedActive($articles);

    $pinned = array_values(array_filter($published, function($article) {
        return (int)$article['anclado'] === 1 && $article['posicion_anclada'] === 'home';
    }));

    $automatic = array_values(array_filter($published, function($article) {
        return !((int)$article['anclado'] === 1 && $article['posicion_anclada'] === 'home');
    }));

    usort($pinned, function($a, $b) {
        return ((int)$a['orden_anclado']) <=> ((int)$b['orden_anclado']);
    });

    $home = $pinned;

    while (count($home) < 4 && !empty($automatic)) {
        $home[] = array_shift($automatic);
    }

    return [
        'home' => $home,
        'general' => $automatic
    ];
}

function renderArticleCard($article, $context = 'default') {
    $status = ((int)$article['activo'] === 0)
        ? 'Oculto'
        : (((int)$article['publicado'] === 1) ? 'Publicado' : 'Borrador');
    ?>
    <div class="article-admin-card">
        <strong><?= htmlspecialchars($article['titulo']) ?></strong>

        <p class="admin-muted">
            <?= htmlspecialchars(trim(($article['author_nombre'] ?? '') . ' ' . ($article['author_apellido'] ?? ''))) ?>
            · <?= $status ?>
            <?= (int)$article['anclado'] === 1 ? ' · Anclado' : '' ?>
        </p>

        <div class="article-admin-actions">
            <a href="edit.php?id=<?= $article['id'] ?>">Editar</a>

            <?php if ((int)$article['publicado'] === 0 && (int)$article['activo'] === 1): ?>
                <a href="article-status.php?id=<?= $article['id'] ?>&action=publish">Publicar</a>
            <?php endif; ?>

            <?php if ((int)$article['publicado'] === 0 && (int)$article['activo'] === 1): ?>
                </div>
                </div>
            <?php
                return;
            endif;
            ?>

            <?php if ((int)$article['activo'] === 1): ?>
                <a href="article-status.php?id=<?= $article['id'] ?>&action=hide">Ocultar</a>
            <?php else: ?>
                <a href="article-status.php?id=<?= $article['id'] ?>&action=reactivate">Reactivar</a>
            <?php endif; ?>

            <?php if ((int)$article['activo'] === 0): ?>
                </div>
                </div>
            <?php
                return;
            endif;
            ?>

            <?php if ($context === 'nuevas_voces_home'): ?>

                <?php if ((int)$article['anclado'] === 1 && $article['posicion_anclada'] === 'home'): ?>
                    <a href="article-pin.php?id=<?= $article['id'] ?>&position=unpin">Desanclar Home</a>
                <?php else: ?>
                    <a href="article-pin.php?id=<?= $article['id'] ?>&position=home&order=1">Anclar Home</a>
                <?php endif; ?>

            <?php elseif ($context === 'nuevas_voces_general'): ?>

                <!-- En General no se ancla -->

            <?php else: ?>

                <?php if ((int)$article['anclado'] === 1): ?>
                    <a href="article-pin.php?id=<?= $article['id'] ?>&position=unpin">Desanclar</a>
                <?php else: ?>
                    <a href="article-pin.php?id=<?= $article['id'] ?>&position=foco&order=1">Anclar Foco</a>
                    <a href="article-pin.php?id=<?= $article['id'] ?>&position=ecos&order=1">Anclar Ecos</a>
                    <a href="article-pin.php?id=<?= $article['id'] ?>&position=trama&order=1">Anclar Trama</a>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
    <?php
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Artículos | FORMA CMS</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>

<div class="admin-shell">

    <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="admin-main">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px;">
            <h1 class="admin-title" style="margin-bottom:0;">Artículos</h1>
            <a href="create.php" class="admin-btn">Nuevo artículo</a>
        </div>

        <?php foreach ($categories as $category): ?>

            <?php
                $categoryArticles = articlesByCategory($allArticles, $category['id']);
                $isNuevasVoces = $category['slug'] === 'nuevas-voces';
            ?>

            <details class="admin-card category-panel">
                <summary class="category-summary">
                    <?= htmlspecialchars($category['nombre']) ?>
                </summary>

                <?php if (empty($categoryArticles)): ?>

                    <p class="admin-muted">No hay artículos en esta categoría.</p>

                <?php else: ?>

                <?php if ($isNuevasVoces): ?>

                    <?php $nvSections = buildNuevasVocesSections($categoryArticles); ?>

                    <h3 class="editorial-section-title">Home</h3>

                    <?php if (empty($nvSections['home'])): ?>
                        <p class="admin-muted">No hay voces publicadas en home.</p>
                    <?php else: ?>
                        <?php foreach ($nvSections['home'] as $article) renderArticleCard($article, 'nuevas_voces_home'); ?>
                    <?php endif; ?>

                    <h3 class="editorial-section-title">General</h3>

                    <?php if (empty($nvSections['general'])): ?>
                        <p class="admin-muted">No hay voces en general.</p>
                    <?php else: ?>
                        <?php foreach ($nvSections['general'] as $article) renderArticleCard($article, 'nuevas_voces_general'); ?>
                    <?php endif; ?>

                <?php else: ?>

                        <?php $sections = buildEditorialSections($categoryArticles); ?>

                        <h3 class="editorial-section-title">En Foco</h3>
                        <?php if (empty($sections['foco'])): ?>
                            <p class="admin-muted">Sin artículo en foco.</p>
                        <?php else: ?>
                            <?php foreach ($sections['foco'] as $article) renderArticleCard($article); ?>
                        <?php endif; ?>

                        <h3 class="editorial-section-title">Ecos</h3>
                        <?php if (empty($sections['ecos'])): ?>
                            <p class="admin-muted">Sin artículos en Ecos.</p>
                        <?php else: ?>
                            <?php foreach ($sections['ecos'] as $article) renderArticleCard($article); ?>
                        <?php endif; ?>

                        <h3 class="editorial-section-title">La Trama</h3>
                        <?php if (empty($sections['trama'])): ?>
                            <p class="admin-muted">Sin artículos en La Trama.</p>
                        <?php else: ?>
                            <?php foreach ($sections['trama'] as $article) renderArticleCard($article); ?>
                        <?php endif; ?>

                    <?php endif; ?>

                    <h3 class="editorial-section-title">Borradores</h3>
                    <?php $drafts = drafts($categoryArticles); ?>
                    <?php if (empty($drafts)): ?>
                        <p class="admin-muted">Sin borradores.</p>
                    <?php else: ?>
                        <?php foreach ($drafts as $article) renderArticleCard($article); ?>
                    <?php endif; ?>

                    <h3 class="editorial-section-title">Ocultos</h3>
                    <?php $hidden = hiddenArticles($categoryArticles); ?>
                    <?php if (empty($hidden)): ?>
                        <p class="admin-muted">Sin artículos ocultos.</p>
                    <?php else: ?>
                        <?php foreach ($hidden as $article) renderArticleCard($article); ?>
                    <?php endif; ?>

                <?php endif; ?>

            </details>

        <?php endforeach; ?>

    </main>

</div>

</body>
</html>