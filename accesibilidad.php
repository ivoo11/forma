<?php
$pageTitle = 'Accesibilidad';
$pageDescription = 'Compromiso de FOЯMA con la accesibilidad, la inclusión digital y una experiencia de lectura abierta para todas las personas.';
$baseUrl = 'https://somosforma.com.ar';
$currentUrl = $baseUrl . '/accesibilidad.php';
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
        '@type' => 'WebPage',
        'name' => $pageTitle . ' | FOЯMA',
        'description' => $pageDescription,
        'url' => $currentUrl,
        'image' => $pageImage,
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'FOЯMA',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $baseUrl . '/assets/img/logo2.png'
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

    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

<header class="accessibility-hero">

    <div class="header-bg-wordmark" aria-hidden="true">
        <img src="/assets/img/logo2.png" alt="">
    </div>

    <div class="container">
        <nav class="main-nav">
            <div class="nav-group">

                <a href="/" class="nav-logo">
                    <img src="/assets/img/logo2.png" alt="FOЯMA">
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
    </div>

    <div class="editorial-container accessibility-hero-content">

        <span>ACCESIBILIDAD</span>

        <h1>
            Diseñar también es hacer lugar.
        </h1>

        <p>
            Trabajamos para que FOЯMA sea una experiencia clara, legible y accesible
            para la mayor cantidad posible de personas.
        </p>

    </div>

</header>

<main class="accessibility-main">

    <section class="accessibility-section">
        <div class="editorial-container">

            <article class="accessibility-block">
                <h2>Una web pensada para ser leída.</h2>

                <p>
                    FOЯMA busca construir una experiencia editorial usable, ordenada y comprensible.
                    Por eso prestamos atención a la jerarquía visual, el contraste, la legibilidad,
                    los tamaños tipográficos y la organización de los contenidos.
                </p>
            </article>

            <article class="accessibility-block">
                <h2>Mejora continua.</h2>

                <p>
                    La accesibilidad no es una tarea cerrada. Es un proceso permanente.
                    A medida que el proyecto crezca, seguiremos revisando componentes,
                    interacciones, navegación y criterios de lectura para mejorar la experiencia.
                </p>
            </article>

            <article class="accessibility-block">
                <h2>Si encontrás una barrera, queremos saberlo.</h2>

                <p>
                    Si tenés dificultades para acceder a algún contenido, navegar una sección
                    o utilizar alguna funcionalidad del sitio, podés escribirnos para que podamos
                    revisarlo y corregirlo.
                </p>

                <a href="mailto:hola@somosforma.com.ar">
                    hola@somosforma.com.ar
                </a>
            </article>

        </div>
    </section>

</main>

<footer class="site-footer">
    <div class="footer-inner">

        <div class="footer-left">
            <img src="/assets/img/logo2.png" alt="FOЯMA" class="footer-logo">

            <nav class="footer-links">
                <a href="/contacto">Contacto</a>
                <a href="/archivo">Archivo</a>
                <a href="/acerca">Acerca de FOЯMA</a>
                <a href="/faq">FAQ</a>
                <a href="/accesibilidad">Accesibilidad</a>
                <a href="/terminos">Términos y Condiciones</a>
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

<script src="/js/main.js"></script>

</body>
</html>