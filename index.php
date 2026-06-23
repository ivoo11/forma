<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/editorial.php';

$pageTitle = 'FOЯMA | Diseño + Comunicación';
$pageDescription = 'Plataforma editorial sobre comunicación, branding, cultura, digital, medios y política.';

$categoriesStmt = $pdo->query("
    SELECT *
    FROM categories
    WHERE activa = 1
    AND slug != 'nuevas-voces'
    ORDER BY orden ASC
");

$categories = $categoriesStmt->fetchAll();

$focusArticles = [];

foreach ($categories as $category) {

    $articlesStmt = $pdo->prepare("
        SELECT
            a.*,
            au.nombre AS author_nombre,
            au.apellido AS author_apellido,
            c.nombre AS category_nombre,
            c.slug AS category_slug
        FROM articles a
        LEFT JOIN authors au ON a.author_id = au.id
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.category_id = :category_id
        AND a.publicado = 1
        AND a.activo = 1
        ORDER BY a.fecha_publicacion DESC, a.id DESC
    ");

    $articlesStmt->execute([
        'category_id' => $category['id']
    ]);

    $articles = $articlesStmt->fetchAll();

    $sections = buildEditorialSections($articles);

    if (!empty($sections['foco'])) {
        $focusArticles[] = $sections['foco'][0];
    }
}

$nuevasVocesStmt = $pdo->query("
    SELECT
        a.*,
        au.nombre AS author_nombre,
        au.apellido AS author_apellido,
        au.carrera,
        au.universidad,
        au.foto
    FROM articles a
    LEFT JOIN authors au ON a.author_id = au.id
    LEFT JOIN categories c ON a.category_id = c.id
    WHERE c.slug = 'nuevas-voces'
    AND a.publicado = 1
    AND a.activo = 1
    ORDER BY a.fecha_publicacion DESC, a.id DESC
");

$nuevasVocesArticles = $nuevasVocesStmt->fetchAll();

$nuevasVocesSections = buildNuevasVocesSections($nuevasVocesArticles);

$homeVoices = $nuevasVocesSections['home'];

$activeQuestionStmt = $pdo->query("
    SELECT *
    FROM questions
    WHERE activa = 1
    ORDER BY fecha_publicacion DESC, id DESC
    LIMIT 1
");

$activeQuestion = $activeQuestionStmt->fetch();

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

    <title>FOЯMA | Diseño, comunicación y cultura digital</title>
    <meta name="description" content="Plataforma editorial sobre diseño, comunicación, branding, cultura digital, medios y política. Una mirada sobre las ideas que moldean nuestro tiempo.">
    <link rel="canonical" href="https://somosforma.com.ar/">

    <meta property="og:site_name" content="FOЯMA">
    <meta property="og:type" content="website">
    <meta property="og:title" content="FOЯMA | Diseño, comunicación y cultura digital">
    <meta property="og:description" content="Una mirada sobre las ideas que moldean nuestro tiempo.">
    <meta property="og:image" content="https://somosforma.com.ar/assets/img/og.jpg">
    <meta property="og:url" content="https://somosforma.com.ar/">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="FOЯMA | Diseño, comunicación y cultura digital">
    <meta name="twitter:description" content="Una mirada sobre las ideas que moldean nuestro tiempo.">
    <meta name="twitter:image" content="https://somosforma.com.ar/assets/img/og.jpg">

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "FOЯMA",
        "url": "https://somosforma.com.ar/",
        "description": "Plataforma editorial sobre diseño, comunicación, branding, cultura digital, medios y política.",
        "publisher": {
            "@type": "Organization",
            "name": "FOЯMA",
            "logo": {
                "@type": "ImageObject",
                "url": "https://somosforma.com.ar/assets/img/logo2.png"
            }
        }
    }
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

    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

    <!-- HEADER -->
    <header class="site-header">

        <div class="header-bg-logo" aria-hidden="true">
            <img src="/assets/img/logo.png" alt="">
        </div>

        <div class="container">

            <nav class="main-nav">

                <div class="nav-group">

                    <a href="index.php" class="nav-logo">
                        <img src="/assets/img/logo2.png" alt="FOЯMA">
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

            <div class="hero-brand">
                <img src="/assets/img/logo2.png" alt="FOЯMA">
            </div>

            <div class="hero-accent"></div>

        </div>

    </header>

    <!-- MAIN -->
    <main class="site-main">

        <section class="focus-section">

            <div class="editorial-container">

                <header class="section-heading">
                    <h2>En Foco</h2>
                    <span></span>
                </header>

                <div class="focus-carousel" aria-label="Notas en foco">

                    <button class="carousel-btn carousel-btn-prev" type="button" aria-label="Anterior">‹</button>

                <div class="focus-track">
                <?php if (!empty($focusArticles)): ?>
                    <?php foreach ($focusArticles as $article): ?>
                        <a
                            href="/articulo/<?= urlencode($article['slug']) ?>"
                            class="article-card"
                        >
                            <img
                                src="<?= !empty($article['imagen_portada'])
                                    ? htmlspecialchars($article['imagen_portada'])
                                    : 'assets/img/default-article.png' ?>"
                                alt=""
                            >
                            <div class="article-card-content">

                                <span class="article-kicker">
                                    <?= htmlspecialchars($article['category_nombre']) ?>
                                </span>
                                <h3>
                                    <?= htmlspecialchars($article['titulo']) ?>
                                </h3>
                                <time>
                                    <?= date('d/m/Y', strtotime($article['fecha_publicacion'])) ?>
                                </time>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-editorial-state">
                        <h3>Aún no hay artículos en foco.</h3>
                        <p>Las publicaciones destacadas aparecerán aquí.</p>
                    </div>

                <?php endif; ?>

                </div>

                    <button class="carousel-btn carousel-btn-next" type="button" aria-label="Siguiente">›</button>

                </div>

                <div class="section-dots desktop-dots" aria-label="Indicadores del carrusel">
                    <button class="active" type="button" aria-label="Ir al slide 1"></button>
                    <button type="button" aria-label="Ir al slide 2"></button>
                    <button type="button" aria-label="Ir al slide 3"></button>
                </div>

                <div class="section-dots tablet-dots">
                    <button class="active"></button>
                    <button></button>
                    <button></button>
                    <button></button>
                </div>

                <div class="section-dots mobile-dots">
                    <button class="active"></button>
                    <button></button>
                    <button></button>
                    <button></button>
                    <button></button>
                </div>

            </div>

        </section>

        <section class="voices-section">

            <div class="editorial-container">

                <header class="section-heading">
                    <h2>Nuevas Voces</h2>
                    <span></span>
                </header>

                <div class="voices-grid">
                <?php if (!empty($homeVoices)): ?>
                    <?php foreach ($homeVoices as $voice): ?>
                        <a
                            href="/articulo/<?= urlencode($voice['slug']) ?>"
                            class="voice-card"
                        >
                            <img
                                src="/<?= htmlspecialchars(
                                    ltrim(
                                        !empty($voice['foto'])
                                            ? $voice['foto']
                                            : 'assets/img/default-avatar.png',
                                        '/'
                                    )
                                ) ?>"
                                alt=""
                            >
                            <h3>
                                <?= htmlspecialchars(
                                    trim(
                                        ($voice['author_nombre'] ?? '') . ' ' .
                                        ($voice['author_apellido'] ?? '')
                                    )
                                ) ?>
                            </h3>
                            <p>
                                <?= htmlspecialchars($voice['carrera'] ?? '') ?>

                                <?php if (!empty($voice['universidad'])): ?>
                                    · <?= htmlspecialchars($voice['universidad']) ?>
                                <?php endif; ?>
                            </p>
                            <span>
                                <?= htmlspecialchars($voice['titulo']) ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>

                    <div class="empty-editorial-state">
                        <h3>Aún no hay voces publicadas.</h3>
                        <p>Las nuevas voces aparecerán aquí.</p>
                    </div>

                <?php endif; ?>

                </div>

                <div class="voices-more">
                    <a href="nuevasvoces.php">Ver todas las voces →</a>
                </div>

            </div>

        </section>

        <?php if ($activeQuestion): ?>
        <section class="question-section">
            <div class="question-bg"></div>
            <div class="editorial-container">
                <span class="question-label">LA PREGUNTA</span>

                <h2 class="question-title">
                    <?= htmlspecialchars($activeQuestion['pregunta']) ?>
                </h2>

                <?php if (!empty($activeQuestion['bajada'])): ?>
                    <p class="question-text">
                        <?= htmlspecialchars($activeQuestion['bajada']) ?>
                    </p>
                <?php endif; ?>

                <a href="pregunta.php?slug=<?= urlencode($activeQuestion['slug']) ?>" class="question-link">
                    Explorar perspectivas →
                </a>
            </div>
        </section>
        <?php endif; ?>

    </main>

    <footer class="site-footer">
        <div class="footer-inner">

            <div class="footer-left">
                <img src="/assets/img/logo2.png" alt="FOЯMA" class="footer-logo">

                <nav class="footer-links">
                    <a href="contacto.php">Contacto</a>
                    <a href="archivo.php">Archivo</a>
                    <a href="acercade.php">Acerca de FOЯMA</a>
                    <a href="faq.php">FAQ</a>
                    <a href="accesibilidad.php">Accesibilidad</a>
                    <a href="terminos.php">Términos y Condiciones</a>
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

    <!-- JS -->
    <script src="/js/main.js"></script>

</body>
</html>