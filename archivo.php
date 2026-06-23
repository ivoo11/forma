<?php
require_once __DIR__ . '/config/database.php';

$categories = $pdo->query("
    SELECT id, nombre, slug
    FROM categories
    WHERE activa = 1
    ORDER BY orden ASC, nombre ASC
")->fetchAll();

$stmt = $pdo->query("
    SELECT 
        a.*,
        au.nombre AS author_nombre,
        au.apellido AS author_apellido,
        au.universidad,
        c.nombre AS category_nombre,
        c.slug AS category_slug
    FROM articles a
    LEFT JOIN authors au ON a.author_id = au.id
    LEFT JOIN categories c ON a.category_id = c.id
    WHERE a.publicado = 1
    AND a.activo = 1
    ORDER BY a.fecha_publicacion DESC, a.id DESC
");

$articles = $stmt->fetchAll();

$totalPublications = count($articles);

$totalAuthors = $pdo->query("
    SELECT COUNT(DISTINCT author_id)
    FROM articles
    WHERE publicado = 1
    AND activo = 1
")->fetchColumn();

$totalUniversities = $pdo->query("
    SELECT COUNT(DISTINCT au.universidad)
    FROM articles a
    LEFT JOIN authors au ON a.author_id = au.id
    WHERE a.publicado = 1
    AND a.activo = 1
    AND au.universidad IS NOT NULL
    AND au.universidad <> ''
")->fetchColumn();

$totalCategories = $pdo->query("
    SELECT COUNT(DISTINCT category_id)
    FROM articles
    WHERE publicado = 1
    AND activo = 1
")->fetchColumn();

$pageTitle = 'Archivo';
$pageDescription = 'Artículos, voces, análisis y publicaciones organizadas para explorar el archivo editorial completo.';
$baseUrl = 'https://somosforma.com.ar';
$currentUrl = $baseUrl . '/archivo.php';
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

<header class="archive-hero">
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

    <div class="editorial-container archive-hero-content">
        <span>ARCHIVO</span>
        <h1>Todo lo que se FORMÓ en la comunidad.</h1>
        <p>Artículos, voces, análisis y publicaciones organizadas para explorar el archivo editorial completo.</p>
    </div>
</header>

<main class="archive-main">

    <section class="archive-tools">
        <div class="editorial-container">

            <div class="archive-stats">
                <div>
                    <strong class="counter" data-target="<?= (int)$totalPublications ?>">0</strong>
                    <span>publicaciones</span>
                </div>
                <div>
                    <strong class="counter" data-target="<?= (int)$totalAuthors ?>">0</strong>
                    <span>autores</span>
                </div>
                <div>
                    <strong class="counter" data-target="<?= (int)$totalUniversities ?>">0</strong>
                    <span>universidades</span>
                </div>
                <div>
                    <strong class="counter" data-target="<?= (int)$totalCategories ?>">0</strong>
                    <span>categorías</span>
                </div>
            </div>

            <div class="archive-search">
                <input 
                    type="search" 
                    id="archiveSearchInput"
                    placeholder="Buscar por título, autor o palabra clave..."
                >
            </div>

            <div class="archive-filters">
                <button class="active" data-category="all">Todo</button>

                <?php foreach ($categories as $category): ?>
                    <button data-category="<?= htmlspecialchars($category['slug']) ?>">
                        <?= htmlspecialchars($category['nombre']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <section class="archive-results">
        <div class="editorial-container">

            <div class="archive-grid" id="archiveGrid">

                <?php foreach ($articles as $article): ?>
                    <?php
                        $authorName = trim(($article['author_nombre'] ?? '') . ' ' . ($article['author_apellido'] ?? ''));
                        $isVoice = $article['category_slug'] === 'nuevas-voces';

                        $searchText = strtolower(
                            $article['titulo'] . ' ' .
                            $authorName . ' ' .
                            ($article['category_nombre'] ?? '') . ' ' .
                            ($article['universidad'] ?? '')
                        );
                    ?>

                    <a 
                        href="/articulo/<?= urlencode($article['slug']) ?>" 
                        class="archive-card <?= $isVoice ? 'archive-card-voice' : '' ?>"
                        data-category="<?= htmlspecialchars($article['category_slug']) ?>"
                        data-search="<?= htmlspecialchars($searchText) ?>"
                    >
                        <span><?= htmlspecialchars($article['category_nombre']) ?></span>
                        <h2><?= htmlspecialchars($article['titulo']) ?></h2>

                        <p>
                            Por <?= htmlspecialchars($authorName) ?>
                            <?php if ($isVoice && !empty($article['universidad'])): ?>
                                · <?= htmlspecialchars($article['universidad']) ?>
                            <?php else: ?>
                                · <?= date('d/m/Y', strtotime($article['fecha_publicacion'])) ?>
                            <?php endif; ?>
                        </p>
                    </a>

                <?php endforeach; ?>

            </div>

            <div class="empty-editorial-state" id="archiveEmptyState" style="display:none;">
                <h3>No encontramos publicaciones.</h3>
                <p>Probá con otra búsqueda o cambiá el filtro seleccionado.</p>
            </div>

        </div>
    </section>

</main>

<script>
const archiveInput = document.querySelector('#archiveSearchInput');
const archiveCards = document.querySelectorAll('.archive-card');
const filterButtons = document.querySelectorAll('.archive-filters button');
const emptyState = document.querySelector('#archiveEmptyState');

let activeCategory = 'all';

function filterArchive() {
    const query = archiveInput.value.toLowerCase().trim();
    let visibleCount = 0;

    archiveCards.forEach(card => {
        const text = card.dataset.search || '';
        const category = card.dataset.category || '';

        const matchesSearch = text.includes(query);
        const matchesCategory = activeCategory === 'all' || category === activeCategory;

        const visible = matchesSearch && matchesCategory;

        card.style.display = visible ? '' : 'none';

        if (visible) visibleCount++;
    });

    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
}

filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        filterButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        activeCategory = button.dataset.category;
        filterArchive();
    });
});

archiveInput.addEventListener('input', filterArchive);
</script>

<script src="/js/main.js"></script>

</body>
</html>