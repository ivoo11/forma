<?php
require_once __DIR__ . '/config/database.php';

$catSlug = $_GET['cat'] ?? '';

if ($catSlug === '') {
    http_response_code(404);
    include '404.html';
    exit;
}

$categoryStmt = $pdo->prepare("
    SELECT *
    FROM categories
    WHERE slug = :slug
    AND activa = 1
    LIMIT 1
");
$categoryStmt->execute(['slug' => $catSlug]);
$category = $categoryStmt->fetch();

if (!$category || $category['slug'] === 'nuevas-voces') {
    http_response_code(404);
    include '404.html';
    exit;
}

$articlesStmt = $pdo->prepare("
    SELECT
        a.*,
        au.nombre AS author_nombre,
        au.apellido AS author_apellido
    FROM articles a
    LEFT JOIN authors au ON a.author_id = au.id
    WHERE a.category_id = :category_id
    AND a.publicado = 1
    AND a.activo = 1
    ORDER BY a.fecha_publicacion DESC, a.id DESC
");
$articlesStmt->execute(['category_id' => $category['id']]);
$articles = $articlesStmt->fetchAll();

function buildCategorySections($articles) {
    $pinned = array_values(array_filter($articles, function($article) {
        return (int)$article['anclado'] === 1;
    }));

    $automatic = array_values(array_filter($articles, function($article) {
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

function articleUrl($article) {
    return 'articulo.php?slug=' . urlencode($article['slug']);
}

function articleImage($article) {
    return !empty($article['imagen_portada']) ? $article['imagen_portada'] : 'assets/img/default-article.png';
}

$sections = buildCategorySections($articles);

$pageTitle = $category['nombre'];
$pageDescription = $category['descripcion'] ?: 'Una mirada de FORMA sobre diseño, comunicación, medios y cultura digital.';
$baseUrl = 'https://somosforma.com.ar';
$currentUrl = $baseUrl . '/categoria.php?cat=' . urlencode($category['slug']);
$pageImage = $baseUrl . '/assets/img/og.jpg';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-HMEQ5Z3K2S"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-HMEQ5Z3K2S');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | FOЯMA</title>

    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($currentUrl) ?>">

    <meta property="og:site_name" content="FOЯMA">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?> | FOЯMA">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($pageImage) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($currentUrl) ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?> | FOЯMA">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($pageImage) ?>">

    <script type="application/ld+json">
    <?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => $pageTitle . ' | FOЯMA',
        'description' => $pageDescription,
        'url' => $currentUrl,
        'image' => $pageImage,
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'FOЯMA',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => 'https://somosforma.com.ar/assets/img/logo2.png'
            ]
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>

    <link rel="icon" type="image/png" href="/assets/img/favicon/favicon-96x96.png?v=20260609" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/assets/img/favicon/favicon.svg?v=20260609">
    <link rel="shortcut icon" href="/assets/img/favicon/favicon.ico?v=20260609">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicon/apple-touch-icon.png?v=20260609">
    <meta name="apple-mobile-web-app-title" content="Forma">
    <link rel="manifest" href="/assets/img/favicon/site.webmanifest?v=20260609">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header class="category-header">

    <div class="header-bg-wordmark" aria-hidden="true">
        <img src="assets/img/logo2.png" alt="">
    </div>

    <div class="container">

        <nav class="main-nav">
            <div class="nav-group">

                <a href="index.php" class="nav-logo">
                    <img src="assets/img/logo2.png" alt="FORMA">
                </a>

                <ul class="nav-menu">
                    <li><a href="categoria.php?cat=branding">Branding</a></li>
                    <li><a href="categoria.php?cat=cultura">Cultura</a></li>
                    <li><a href="categoria.php?cat=digital">Digital</a></li>
                    <li><a href="categoria.php?cat=medios">Medios</a></li>
                    <li><a href="categoria.php?cat=politica">Política</a></li>
                </ul>

            </div>
        </nav>

        <div class="category-hero-title">
            <h1><?= htmlspecialchars($category['nombre']) ?></h1>
        </div>

        <div class="category-accent"></div>

    </div>

</header>

<main class="site-main">

    <section class="category-focus-section">
        <div class="editorial-container">

            <header class="section-heading section-heading-violet">
                <h2>En Foco</h2>
                <span></span>
            </header>

            <?php if (!empty($sections['foco'])): ?>
                <?php $article = $sections['foco'][0]; ?>

                <a href="<?= htmlspecialchars(articleUrl($article)) ?>" class="category-feature-card">
                    <img src="<?= htmlspecialchars(articleImage($article)) ?>" alt="">

                    <div class="category-feature-overlay"></div>

                    <div class="category-feature-content">
                        <span><?= htmlspecialchars($category['nombre']) ?></span>
                        <h3><?= htmlspecialchars($article['titulo']) ?></h3>
                        <time>
                            <?= date('d/m/Y', strtotime($article['fecha_publicacion'])) ?>
                        </time>
                    </div>
                </a>

            <?php else: ?>
                <div class="empty-editorial-state">
                    <h3>Aún no hay una mirada en foco.</h3>
                    <p>Esta sección se actualizará cuando se publique nuevo contenido.</p>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <section class="category-ecos-section">
        <div class="editorial-container">

            <header class="section-heading section-heading-violet">
                <h2>Ecos</h2>
                <span></span>
            </header>

            <?php if (!empty($sections['ecos'])): ?>

            <div class="category-ecos-grid">

                <?php foreach ($sections['ecos'] as $article): ?>
                    <a href="<?= htmlspecialchars(articleUrl($article)) ?>" class="category-ecos-card">
                        <img src="<?= htmlspecialchars(articleImage($article)) ?>" alt="">

                        <div class="category-ecos-content">
                            <span><?= htmlspecialchars($category['nombre']) ?></span>
                            <h3><?= htmlspecialchars($article['titulo']) ?></h3>
                            <time>
                                <?= date('d/m/Y', strtotime($article['fecha_publicacion'])) ?>
                            </time>
                        </div>
                    </a>
                <?php endforeach; ?>

            </div>

            <?php else: ?>

            <div class="empty-editorial-state">
                <h3>No hay ecos disponibles.</h3>
                <p>Las conversaciones continúan construyéndose.</p>
            </div>

            <?php endif; ?>

        </div>
    </section>

    <section class="category-trama-section">
        <div class="editorial-container">

            <header class="section-heading section-heading-violet">
                <h2>La Trama</h2>
                <span></span>
            </header>

            <div class="category-trama-grid">

                <?php foreach ($sections['trama'] as $article): ?>
                    <a href="<?= htmlspecialchars(articleUrl($article)) ?>" class="trama-card">
                        <img src="<?= htmlspecialchars(articleImage($article)) ?>" alt="">

                        <div class="trama-card-content">
                            <span class="article-kicker"><?= htmlspecialchars($category['nombre']) ?></span>
                            <h3><?= htmlspecialchars($article['titulo']) ?></h3>
                            <time>
                                <?= date('d/m/Y', strtotime($article['fecha_publicacion'])) ?>
                            </time>
                        </div>
                    </a>
                <?php endforeach; ?>

            </div>

            <div class="empty-editorial-state">
                <h3>La trama todavía se está escribiendo.</h3>
                <p>Próximamente se incorporarán nuevas publicaciones.</p>
            </div>

            <div class="category-more">
                <a href="archivo.php?cat=<?= htmlspecialchars($category['slug']) ?>">
                    Ver más artículos →
                </a>
            </div>

        </div>
    </section>

</main>

<footer class="site-footer">
    <div class="footer-inner">

        <div class="footer-left">
            <img src="assets/img/logo2.png" alt="FOЯMA" class="footer-logo">

            <nav class="footer-links">
                <a href="#">Contacto</a>
                <a href="archivo.php">Archivo</a>
                <a href="#">Acerca de nosotros</a>
                <a href="#">FAQ</a>
                <a href="#">Accesibilidad</a>
                <a href="#">Términos y Condiciones</a>
            </nav>

            <p>2026 SOMOSFOЯMA. Todos los derechos reservados</p>
        </div>

        <div class="footer-social">
            <a href="#" aria-label="X">
                <i class="fa-brands fa-x-twitter"></i>
            </a>
            <a href="#" aria-label="Instagram">
                <i class="fa-brands fa-instagram"></i>
            </a>
            <a href="#" aria-label="LinkedIn">
                <i class="fa-brands fa-linkedin-in"></i>
            </a>
        </div>

    </div>
</footer>

<script src="js/main.js"></script>

</body>
</html>