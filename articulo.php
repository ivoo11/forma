<?php
require_once __DIR__ . '/config/database.php';

$slug = $_GET['slug'] ?? '';

if ($slug === '') {
    http_response_code(404);
    include '404.html';
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        a.*,
        au.nombre AS author_nombre,
        au.apellido AS author_apellido,
        au.tipo_autor,
        au.cargo,
        au.institucion,
        au.carrera,
        au.universidad,
        au.bio,
        au.foto,
        c.nombre AS category_nombre,
        c.slug AS category_slug
    FROM articles a
    LEFT JOIN authors au ON a.author_id = au.id
    LEFT JOIN categories c ON a.category_id = c.id
    WHERE a.slug = :slug
    AND a.publicado = 1
    AND a.activo = 1
    LIMIT 1
");

$stmt->execute(['slug' => $slug]);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
    include '404.html';
    exit;
}

$blocksStmt = $pdo->prepare("
    SELECT *
    FROM article_blocks
    WHERE article_id = :article_id
    ORDER BY orden ASC
");
$blocksStmt->execute(['article_id' => $article['id']]);
$blocks = $blocksStmt->fetchAll();

$isVoiceArticle = $article['category_slug'] === 'nuevas-voces';

$sectionLabel = '';

if (!$isVoiceArticle) {

    $catStmt = $pdo->prepare("
        SELECT
            a.id,
            a.category_id,
            a.publicado,
            a.activo,
            a.anclado,
            a.posicion_anclada,
            a.orden_anclado,
            a.fecha_publicacion
        FROM articles a
        WHERE a.category_id = :category_id
        AND a.publicado = 1
        AND a.activo = 1
        ORDER BY a.fecha_publicacion DESC, a.id DESC
    ");

    $catStmt->execute([
        'category_id' => $article['category_id']
    ]);

    $categoryArticles = $catStmt->fetchAll();

    $pinned = array_values(array_filter($categoryArticles, function($a) {
        return (int)$a['anclado'] === 1;
    }));

    $automatic = array_values(array_filter($categoryArticles, function($a) {
        return (int)$a['anclado'] !== 1;
    }));

    $sections = [
        'foco' => [],
        'ecos' => [],
        'trama' => []
    ];

    foreach ($pinned as $a) {

        $pos = $a['posicion_anclada'];

        if (isset($sections[$pos])) {
            $sections[$pos][] = $a;
        }
    }

    foreach (['foco','ecos','trama'] as $section) {

        usort($sections[$section], function($a, $b) {
            return ((int)$a['orden_anclado']) <=> ((int)$b['orden_anclado']);
        });
    }

    while (count($sections['foco']) < 1 && !empty($automatic)) {
        $sections['foco'][] = array_shift($automatic);
    }

    while (count($sections['ecos']) < 2 && !empty($automatic)) {
        $sections['ecos'][] = array_shift($automatic);
    }

    while (count($sections['trama']) < 6 && !empty($automatic)) {
        $sections['trama'][] = array_shift($automatic);
    }

    $articleId = (int)$article['id'];

    foreach ($sections['foco'] as $a) {
        if ((int)$a['id'] === $articleId) {
            $sectionLabel = 'En Foco';
        }
    }

    foreach ($sections['ecos'] as $a) {
        if ((int)$a['id'] === $articleId) {
            $sectionLabel = 'Ecos';
        }
    }

    foreach ($sections['trama'] as $a) {
        if ((int)$a['id'] === $articleId) {
            $sectionLabel = 'La Trama';
        }
    }
}

$pageTitle = $article['og_title'] ?: $article['titulo'];

$pageDescription = $article['og_description'] ?: $article['bajada'];
if (!$pageDescription) {
    $pageDescription = 'Una mirada de FORMA sobre diseño, comunicación, medios y cultura digital.';
}

$pageImage = $article['og_image'] ?: ($isVoiceArticle ? $article['foto'] : $article['imagen_portada']);

$baseUrl = 'https://somosforma.com.ar';
$currentUrl = $baseUrl . '/articulo.php?slug=' . urlencode($article['slug']);

$shareTitle = urlencode($article['titulo']);
$shareUrl = urlencode($currentUrl);

if ($pageImage && !str_starts_with($pageImage, 'http')) {
    $pageImage = $baseUrl . '/' . ltrim($pageImage, '/');
}
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
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($pageImage) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($currentUrl) ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($pageImage) ?>">

    <?php
    $articleAuthor = trim(($article['author_nombre'] ?? '') . ' ' . ($article['author_apellido'] ?? ''));

    $articleJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => $isVoiceArticle ? 'OpinionNewsArticle' : 'Article',
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $currentUrl
        ],
        'headline' => $article['titulo'],
        'description' => $pageDescription,
        'image' => $pageImage,
        'datePublished' => !empty($article['fecha_publicacion'])
            ? date('c', strtotime($article['fecha_publicacion']))
            : null,
        'dateModified' => !empty($article['fecha_actualizacion'])
            ? date('c', strtotime($article['fecha_actualizacion']))
            : (!empty($article['fecha_publicacion']) ? date('c', strtotime($article['fecha_publicacion'])) : null),
        'author' => [
            '@type' => 'Person',
            'name' => $articleAuthor ?: 'FOЯMA'
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'FOЯMA',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $baseUrl . '/assets/img/logo2.png'
            ]
        ],
        'articleSection' => $article['category_nombre'] ?? null
    ];

    $articleJsonLd = array_filter($articleJsonLd, fn($value) => $value !== null && $value !== '');
    ?>

    <script type="application/ld+json">
    <?= json_encode($articleJsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>

    <link rel="icon" type="image/png" href="/assets/img/favicon/favicon-96x96.png?v=20260609" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="/assets/img/favicon/favicon.svg?v=20260609">
    <link rel="shortcut icon" href="/assets/img/favicon/favicon.ico?v=20260609">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicon/apple-touch-icon.png?v=20260609">
    <meta name="apple-mobile-web-app-title" content="Forma">
    <link rel="manifest" href="/assets/img/favicon/site.webmanifest?v=20260609">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/css/style.css">
</head>

<body class="<?= $isVoiceArticle ? 'voice-article-page' : '' ?>">

<?php if ($isVoiceArticle): ?>

<header class="voice-article-hero">

    <div class="header-bg-wordmark" aria-hidden="true">
        <img src="/assets/img/logo2.png" alt="">
    </div>

    <div class="container">

        <nav class="main-nav">
            <div class="nav-group">

                <a href="index.php" class="nav-logo">
                    <img src="/assets/img/logo2.png" alt="FORMA">
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

    </div>

    <div class="editorial-container voice-hero-content">

        <span class="voice-label">
            NUEVAS VOCES
            <?php if (!empty($article['carrera'])): ?>
                / <?= htmlspecialchars(mb_strtoupper($article['carrera'])) ?>
            <?php endif; ?>
        </span>

        <h1><?= htmlspecialchars($article['titulo']) ?></h1>

        <div class="voice-author-hero">
            <div>
                <span>UNA VOZ DE</span>

                <h2>
                    <?= htmlspecialchars($article['author_nombre'] . ' ' . $article['author_apellido']) ?>
                </h2>

                <p>
                    <?= htmlspecialchars($article['carrera'] ?? '') ?>
                    <?php if (!empty($article['universidad'])): ?>
                        · <?= htmlspecialchars($article['universidad']) ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>

    </div>

</header>

<?php else: ?>

<header class="article-header">

    <div class="header-bg-wordmark" aria-hidden="true">
        <img src="/assets/img/logo2.png" alt="">
    </div>

    <div class="container">

        <nav class="main-nav">
            <div class="nav-group">

                <a href="/" class="nav-logo">
                    <img src="/assets/img/logo2.png" alt="FORMA">
                </a>

                <ul class="nav-menu">
                    <li><a href="/branding">Branding</a></li>
                    <li><a href="/cultura">Cultura</a></li>
                    <li><a href="/digital">Digital</a></li>
                    <li><a href="/medios">Medios</a></li>
                    <li><a href="/politica">Política</a></li>
                </ul>

            </div>
        </nav>

        <div class="article-hero-label">
            <h1>
            <?php if ($sectionLabel): ?>
                <?= htmlspecialchars($article['category_nombre']) ?>
                / <?= $sectionLabel ?>
            <?php else: ?>
                ARCHIVO
                / <?= htmlspecialchars($article['category_nombre']) ?>
            <?php endif; ?>
            </h1>
        </div>

        <div class="category-accent"></div>

    </div>

</header>

<?php endif; ?>

<main class="<?= $isVoiceArticle ? 'voice-article-main' : 'article-main' ?>">

    <article class="<?= $isVoiceArticle ? 'voice-article-layout' : 'article-layout' ?>">

        <div class="editorial-container">

            <?php if (!$isVoiceArticle): ?>

                <header class="article-title-block">
                    <h2><?= htmlspecialchars($article['titulo']) ?></h2>

                    <?php if (!empty($article['bajada'])): ?>
                        <p><?= htmlspecialchars($article['bajada']) ?></p>
                    <?php endif; ?>

                    <div class="article-meta">
                        <span>
                            Por <?= htmlspecialchars($article['author_nombre'] . ' ' . $article['author_apellido']) ?>
                        </span>

                        <?php if (!empty($article['cargo'])): ?>
                            <span><?= htmlspecialchars($article['cargo']) ?></span>
                        <?php endif; ?>

                        <?php if (!empty($article['institucion'])): ?>
                            <span><?= htmlspecialchars($article['institucion']) ?></span>
                        <?php endif; ?>

                        <span><?= htmlspecialchars($article['category_nombre']) ?></span>
                    </div>
                </header>

            <?php endif; ?>

            <?php if ($isVoiceArticle && !empty($article['foto'])): ?>

                <figure class="voice-cover">
                    <img src="<?= htmlspecialchars($article['foto']) ?>" alt="">
                </figure>

            <?php elseif (!$isVoiceArticle && !empty($article['imagen_portada'])): ?>

            <figure class="article-cover">
                <img
                    src="<?= htmlspecialchars(
                        !empty($article['imagen_portada'])
                            ? $article['imagen_portada']
                            : 'assets/img/default-article.png'
                    ) ?>"
                    alt=""
                >
            </figure>

            <?php endif; ?>

            <div class="<?= $isVoiceArticle ? 'voice-article-content' : 'article-content' ?>">

                <?php foreach ($blocks as $block): ?>

                    <?php if ($block['tipo'] === 'paragraph'): ?>
                        <p><?= nl2br(htmlspecialchars($block['contenido'])) ?></p>
                    <?php endif; ?>

                    <?php if ($block['tipo'] === 'heading'): ?>
                        <h3><?= htmlspecialchars($block['contenido']) ?></h3>
                    <?php endif; ?>

                    <?php if ($block['tipo'] === 'highlight'): ?>
                        <blockquote><?= nl2br(htmlspecialchars($block['contenido'])) ?></blockquote>
                    <?php endif; ?>

                <?php endforeach; ?>

            </div>

            <?php if ($isVoiceArticle && !empty($article['bio'])): ?>

                <section class="voice-author-box">

                    <?php if (!empty($article['foto'])): ?>
                        <img
                            src="<?= htmlspecialchars(
                                !empty($article['foto'])
                                    ? $article['foto']
                                    : 'assets/img/default-avatar.png'
                            ) ?>"
                            alt=""
                        >
                    <?php endif; ?>

                    <div>
                        <span>Sobre el autor</span>

                        <h3>
                            <?= htmlspecialchars($article['author_nombre'] . ' ' . $article['author_apellido']) ?>
                        </h3>

                        <p><?= nl2br(htmlspecialchars($article['bio'])) ?></p>
                    </div>

                </section>

            <?php endif; ?>

            <section class="article-share <?= $isVoiceArticle ? 'voice-share' : '' ?>">

                <h3>
                    <?= $isVoiceArticle ? '¿Te gustó esta voz?' : '¿Te gustó esta mirada?' ?>
                </h3>

                <p>Compartila.</p>

                <div class="share-links">

                    <a
                        href="https://wa.me/?text=<?= $shareTitle ?>%20<?= $shareUrl ?>"
                        target="_blank"
                        rel="noopener"
                    >
                        WhatsApp
                    </a>

                    <a
                        href="https://twitter.com/intent/tweet?text=<?= $shareTitle ?>&url=<?= $shareUrl ?>"
                        target="_blank"
                        rel="noopener"
                    >
                        X
                    </a>

                    <a
                        href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $shareUrl ?>"
                        target="_blank"
                        rel="noopener"
                    >
                        LinkedIn
                    </a>

                    <a
                        href="#"
                        id="copyLinkBtn"
                    >
                        Copiar enlace
                    </a>

                </div>

            </section>

            <div class="<?= $isVoiceArticle ? 'article-more voice-more' : 'article-more' ?>">

                <?php if ($isVoiceArticle): ?>

                    <a href="nuevasvoces.php">Conocé más voces →</a>

                <?php else: ?>

                    <a href="categoria.php?cat=<?= htmlspecialchars($article['category_slug']) ?>">
                        Más artículos de <?= htmlspecialchars($article['category_nombre']) ?> →
                    </a>

                <?php endif; ?>

            </div>

        </div>

    </article>

</main>

<script>
const copyLinkBtn = document.getElementById('copyLinkBtn');

if (copyLinkBtn) {

    copyLinkBtn.addEventListener('click', async (e) => {

        e.preventDefault();

        try {

            await navigator.clipboard.writeText(window.location.href);

            copyLinkBtn.textContent = '¡Copiado!';

            setTimeout(() => {
                copyLinkBtn.textContent = 'Copiar enlace';
            }, 2000);

        } catch (err) {

            alert('No se pudo copiar el enlace.');

        }

    });

}
</script>

<script src="/js/main.js"></script>

</body>
</html>