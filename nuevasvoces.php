<?php
require_once __DIR__ . '/config/database.php';

$stmt = $pdo->prepare("
    SELECT 
        a.*,
        au.nombre AS author_nombre,
        au.apellido AS author_apellido,
        au.carrera,
        au.universidad,
        au.foto,
        c.nombre AS category_nombre,
        c.slug AS category_slug
    FROM articles a
    LEFT JOIN authors au ON a.author_id = au.id
    LEFT JOIN categories c ON a.category_id = c.id
    WHERE c.slug = 'nuevas-voces'
    AND a.publicado = 1
    AND a.activo = 1
    ORDER BY a.fecha_publicacion DESC, a.id DESC
");

$stmt->execute();
$voices = $stmt->fetchAll();

$pageTitle = 'Nuevas Voces';
$pageDescription = 'Miradas emergentes para pensar la comunicación contemporánea.';
$baseUrl = 'https://somosforma.com.ar';
$currentUrl = $baseUrl . '/nuevasvoces.php';
$pageImage = $baseUrl . '/assets/img/og.jpg';

function voiceImage($voice) {
    return !empty($voice['foto']) ? $voice['foto'] : 'assets/img/default-avatar.png';
}

function articleUrl($voice) {
    return 'articulo.php?slug=' . urlencode($voice['slug']);
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
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>

<body class="voices-page">

<header class="voices-page-hero">
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
    </div>

    <div class="editorial-container voices-hero-content">
        <span>NUEVAS VOCES</span>
        <h1>Miradas emergentes para pensar la comunicación contemporánea.</h1>
        <p>Selección de voces invitadas, estudiantes y nuevos perfiles del ecosistema comunicacional.</p>
    </div>
</header>

<main class="voices-page-main">
    <section class="voices-directory">
        <div class="editorial-container">

            <div class="voices-search-intro">
                <span>EXPLORAR VOCES</span>
            </div>

            <div class="voices-search">
                <input
                    type="search"
                    id="voicesSearchInput"
                    placeholder="Buscar por nombre, universidad, tema o título..."
                    aria-label="Buscar en Nuevas Voces"
                >
            </div>

            <div class="voices-directory-grid" id="voicesGrid">

                <?php if (empty($voices)): ?>

                    <div class="empty-editorial-state">
                        <h3>Todavía no hay voces publicadas.</h3>
                        <p>Próximamente se incorporarán nuevas miradas.</p>
                    </div>

                <?php else: ?>

                    <?php foreach ($voices as $voice): ?>
                        <?php
                            $authorFullName = trim(($voice['author_nombre'] ?? '') . ' ' . ($voice['author_apellido'] ?? ''));
                            $searchText = strtolower(
                                $authorFullName . ' ' .
                                ($voice['carrera'] ?? '') . ' ' .
                                ($voice['universidad'] ?? '') . ' ' .
                                ($voice['titulo'] ?? '')
                            );
                        ?>

                        <a 
                            href="<?= htmlspecialchars(articleUrl($voice)) ?>" 
                            class="voice-profile-card"
                            data-search="<?= htmlspecialchars($searchText) ?>"
                        >
                            <img src="<?= htmlspecialchars(voiceImage($voice)) ?>" alt="<?= htmlspecialchars($authorFullName) ?>">

                            <div>
                                <h3><?= htmlspecialchars($authorFullName) ?></h3>

                                <p>
                                    <?= htmlspecialchars($voice['carrera'] ?? '') ?>
                                    <?php if (!empty($voice['universidad'])): ?>
                                        · <?= htmlspecialchars($voice['universidad']) ?>
                                    <?php endif; ?>
                                </p>

                                <span><?= htmlspecialchars($voice['titulo']) ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

            <div class="voices-empty-state" id="voicesEmptyState">
                No encontramos voces con esa búsqueda.
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
            <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
        </div>
    </div>
</footer>

<script>
const voicesInput = document.querySelector('#voicesSearchInput');
const voiceCards = document.querySelectorAll('.voice-profile-card');
const emptyState = document.querySelector('#voicesEmptyState');

if (voicesInput) {
    voicesInput.addEventListener('input', () => {
        const query = voicesInput.value.toLowerCase().trim();
        let visibleCount = 0;

        voiceCards.forEach(card => {
            const text = card.dataset.search || '';
            const match = text.includes(query);

            card.style.display = match ? '' : 'none';

            if (match) visibleCount++;
        });

        emptyState.style.display = visibleCount === 0 && query !== '' ? 'block' : 'none';
    });
}
</script>

<script src="js/main.js"></script>

</body>
</html>