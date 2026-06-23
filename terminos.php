<?php
$pageTitle = 'Términos y Condiciones';
$pageDescription = 'Términos y condiciones de uso de FOЯMA y lineamientos para la utilización de contenidos publicados en la plataforma.';
$baseUrl = 'https://somosforma.com.ar';
$currentUrl = $baseUrl . '/terminos.php';
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
        '@type' => 'ContactPage',
        'name' => $pageTitle . ' | FOЯMA',
        'description' => $pageDescription,
        'url' => $currentUrl,
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

<header class="terms-hero">

    <div class="header-bg-wordmark" aria-hidden="true">
        <img src="assets/img/logo2.png" alt="">
    </div>

    <div class="container">
        <nav class="main-nav">
            <div class="nav-group">

                <a href="index.php" class="nav-logo">
                    <img src="assets/img/logo2.png" alt="FOЯMA">
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

    <div class="editorial-container terms-hero-content">

        <span>TÉRMINOS Y CONDICIONES</span>

        <h1>
            Las ideas circulan mejor cuando las reglas son claras.
        </h1>

        <p>
            Condiciones de uso, publicación, derechos de autor y participación editorial dentro de FOЯMA.
        </p>

    </div>

</header>

<main class="terms-main">

    <section class="terms-section">
        <div class="editorial-container">

            <article class="terms-block">
                <span>01</span>
                <h2>Sobre FORMA</h2>
                <p>
                    FOЯMA es una plataforma editorial independiente dedicada a la publicación, difusión y archivo de contenidos relacionados con comunicación, diseño, medios, cultura digital y política.
                </p>
                <p>
                    El acceso y utilización del sitio implica la aceptación de los presentes términos y condiciones.
                </p>
            </article>

            <article class="terms-block">
                <span>02</span>
                <h2>Propiedad intelectual</h2>
                <p>
                    Todos los contenidos publicados en FOЯMA, incluyendo textos, imágenes, ilustraciones, elementos gráficos, identidad visual, diseño editorial y materiales audiovisuales, se encuentran protegidos por las normas nacionales e internacionales de propiedad intelectual.
                </p>
                <p>
                    Salvo indicación expresa en contrario, los derechos patrimoniales correspondientes a los contenidos publicados pertenecen a sus autores y/o a FOЯMA según corresponda.
                </p>
            </article>

            <article class="terms-block">
                <span>03</span>
                <h2>Contenidos enviados por colaboradores</h2>
                <p>
                    Al enviar artículos, ensayos, investigaciones, entrevistas u otros materiales para su publicación, el autor declara ser titular legítimo de los derechos sobre el material enviado.
                </p>
                <p>
                    También declara que dicho contenido no infringe derechos de terceros y que posee las autorizaciones necesarias para la utilización de imágenes, gráficos, citas o materiales incorporados al trabajo.
                </p>
            </article>

            <article class="terms-block">
                <span>04</span>
                <h2>Autorización de publicación y difusión</h2>
                <p>
                    Mediante el envío voluntario de contenidos a FOЯMA, el autor otorga una autorización gratuita, no exclusiva y revocable para publicar el material en el sitio web, difundirlo en redes sociales y canales institucionales e incorporarlo a archivos editoriales, compilaciones o publicaciones futuras vinculadas al proyecto.
                </p>
                <p>
                    La titularidad intelectual de la obra continúa perteneciendo al autor.
                </p>
            </article>

            <article class="terms-block">
                <span>05</span>
                <h2>Citas y referencias</h2>
                <p>
                    FOЯMA promueve el respeto por la producción intelectual. Los autores son responsables de citar adecuadamente las fuentes utilizadas y de respetar las normas aplicables en materia de derechos de autor, referencias bibliográficas y uso legítimo de contenidos de terceros.
                </p>
                <p>
                    La omisión deliberada de fuentes o la apropiación indebida de material ajeno podrá motivar la remoción inmediata de la publicación.
                </p>
            </article>

            <article class="terms-block">
                <span>06</span>
                <h2>Responsabilidad sobre los contenidos</h2>
                <p>
                    Las opiniones expresadas por autores invitados, colaboradores y participantes de espacios como Nuevas Voces representan exclusivamente la posición de sus respectivos autores.
                </p>
                <p>
                    Dichas opiniones no reflejan necesariamente la postura editorial de FOЯMA.
                </p>
            </article>

            <article class="terms-block">
                <span>07</span>
                <h2>Derecho de edición</h2>
                <p>
                    FOЯMA podrá realizar correcciones ortográficas, ajustes de estilo, adecuaciones de formato y modificaciones editoriales menores destinadas a mejorar la calidad, legibilidad o consistencia de los contenidos publicados.
                </p>
                <p>
                    Las modificaciones sustanciales serán previamente acordadas con el autor cuando corresponda.
                </p>
            </article>

            <article class="terms-block">
                <span>08</span>
                <h2>Remoción de contenidos</h2>
                <p>
                    FOЯMA podrá suspender, modificar o retirar publicaciones que infrinjan derechos de terceros, contengan información manifiestamente falsa o engañosa, promuevan discriminación, violencia o actividades ilícitas, o contravengan los principios editoriales del proyecto.
                </p>
            </article>

            <article class="terms-block">
                <span>09</span>
                <h2>Enlaces externos</h2>
                <p>
                    El sitio puede contener referencias o enlaces a sitios web externos.
                </p>
                <p>
                    FOЯMA no controla ni garantiza el contenido, disponibilidad o políticas de privacidad de dichos servicios.
                </p>
            </article>

            <article class="terms-block">
                <span>10</span>
                <h2>Modificaciones</h2>
                <p>
                    FOЯMA podrá actualizar los presentes términos y condiciones cuando resulte necesario para reflejar cambios editoriales, legales o tecnológicos.
                </p>
                <p>
                    Las modificaciones entrarán en vigencia desde su publicación en el sitio.
                </p>
            </article>

            <article class="terms-block terms-contact">
                <span>11</span>
                <h2>Contacto</h2>
                <p>
                    Para consultas relacionadas con derechos de autor, publicaciones, solicitudes de corrección o remoción de contenidos:
                </p>

                <a href="mailto:hola@somosforma.com.ar">
                    hola@<br>somosforma.com.ar
                </a>
            </article>

        </div>
    </section>

</main>

<footer class="site-footer">
    <div class="footer-inner">

        <div class="footer-left">
            <img src="assets/img/logo2.png" alt="FOЯMA" class="footer-logo">

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
            <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
        </div>

    </div>
</footer>

<script src="/js/main.js"></script>

</body>
</html>