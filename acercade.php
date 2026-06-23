<?php
$pageTitle = 'Acerca de FORMA';
$pageDescription = 'FORMA es una plataforma editorial independiente sobre comunicación, diseño, medios, cultura digital y política.';
$baseUrl = 'https://somosforma.com.ar';
$currentUrl = $baseUrl . '/acerca';
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
        '@type' => 'AboutPage',
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

<header class="about-hero">

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

    <div class="editorial-container about-hero-content">

        <span>ACERCA DE FORMA</span>

        <h1>
            No nacimos para cubrir la actualidad.
            Nacimos para entenderla.
        </h1>

        <p>
            Un espacio editorial para explorar las relaciones entre comunicación,
            diseño, medios, cultura digital y política.
        </p>

    </div>

</header>

<main class="about-main">

    <section class="about-block">
        <div class="editorial-container">

            <header class="section-heading section-heading-violet">
                <h2>¿Qué es FORMA?</h2>
                <span></span>
            </header>

            <div class="about-text">
                <p>
                    FORMA es una plataforma editorial independiente dedicada a explorar
                    las relaciones entre comunicación, diseño, medios, cultura digital
                    y política.
                </p>

                <p>
                    Creemos que las transformaciones más importantes de nuestro tiempo
                    no ocurren únicamente en las instituciones o en los mercados.
                    También ocurren en las formas que utilizamos para comunicar,
                    representar y comprender el mundo.
                </p>
            </div>

        </div>
    </section>

    <section class="about-block about-block-alt">
        <div class="editorial-container">

            <header class="section-heading section-heading-violet">
                <h2>¿Por qué existe?</h2>
                <span></span>
            </header>

            <div class="about-text">
                <p>Vivimos rodeados de mensajes.</p>

                <p>
                    Sin embargo, cada vez tenemos menos espacios para detenernos a pensar
                    cómo esos mensajes son construidos, distribuidos y consumidos.
                </p>

                <p>FORMA nace para recuperar esa conversación.</p>
            </div>

        </div>
    </section>

    <section class="about-disciplines">
        <div class="editorial-container">

            <span class="about-kicker">LO QUE NOS INTERESA</span>

            <div class="discipline-list">
                <h2>COMUNICACIÓN</h2>
                <h2>DISEÑO</h2>
                <h2>MEDIOS</h2>
                <h2>CULTURA</h2>
                <h2>POLÍTICA</h2>
            </div>

            <p>
                No entendemos estas disciplinas como compartimentos separados.
                Las entendemos como parte de una misma conversación.
            </p>

        </div>
    </section>

    <section class="about-manifesto">
        <div class="editorial-container">

            <span class="about-kicker">EN LO QUE CREEMOS</span>

            <div class="manifesto-list">
                <h2>Creemos en las preguntas.</h2>
                <h2>Creemos en las perspectivas diversas.</h2>
                <h2>Creemos que la forma también comunica.</h2>
                <h2>Creemos que el diseño es una herramienta cultural.</h2>
                <h2>Creemos que las ideas mejoran cuando circulan.</h2>
                <h2>Creemos que el debate es más valioso que el algoritmo.</h2>
            </div>

        </div>
    </section>

    <section class="about-closing">
        <div class="editorial-container">

            <h2>FORMA no busca tener la última palabra.</h2>

            <h3>Busca generar mejores conversaciones.</h3>

            <a href="/archivo" class="about-link">
                Explorar el archivo →
            </a>

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