<?php
$pageTitle = 'FAQ';
$pageDescription = 'Preguntas frecuentes sobre FOЯMA, su propuesta editorial, Nuevas Voces, La Pregunta y formas de participación.';
$baseUrl = 'https://somosforma.com.ar';
$currentUrl = $baseUrl . '/faq.php';
$pageImage = $baseUrl . '/assets/img/og.jpg';
?>

<!DOCTYPE html>
<html lang="es">
<head>
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

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header class="faq-hero">

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

    <div class="editorial-container faq-hero-content">
        <span>FAQ</span>

        <h1>
            Preguntas frecuentes para quienes llegan por primera vez.
        </h1>

        <p>
            Una guía rápida para entender qué somos, cómo pensamos y de qué manera podés participar.
        </p>
    </div>

</header>

<main class="faq-main">

    <section class="faq-section">
        <div class="editorial-container">

            <div class="faq-list">

                <article class="faq-item">
                    <button type="button" class="faq-question">
                        <span>¿Son un medio tradicional?</span>
                        <strong>+</strong>
                    </button>

                    <div class="faq-answer">
                        <p>
                            No. FOЯMA nació como una plataforma editorial independiente dedicada a pensar la comunicación,
                            el diseño, los medios, la cultura digital y la política desde una perspectiva contemporánea.
                        </p>
                        <p>
                            No perseguimos la lógica de la noticia permanente ni la cobertura de último momento.
                            Nos interesa comprender fenómenos, construir conversaciones y generar pensamiento.
                        </p>
                    </div>
                </article>

                <article class="faq-item">
                    <button type="button" class="faq-question">
                        <span>¿Es un sitio exclusivo para diseñadores?</span>
                        <strong>+</strong>
                    </button>

                    <div class="faq-answer">
                        <p>
                            No. Aunque el diseño ocupa un lugar importante dentro de FOЯMA, la comunidad está pensada
                            para comunicadores, periodistas, investigadores, estudiantes, profesionales, docentes y
                            cualquier persona interesada en comprender cómo se construye sentido en la sociedad contemporánea.
                        </p>
                    </div>
                </article>

                <article class="faq-item">
                    <button type="button" class="faq-question">
                        <span>¿Quién puede escribir?</span>
                        <strong>+</strong>
                    </button>

                    <div class="faq-answer">
                        <p>
                            Cualquier persona con una mirada relevante para aportar. Algunos contenidos son producidos
                            por el equipo editorial y otros forman parte de espacios abiertos como Nuevas Voces.
                        </p>
                    </div>
                </article>

                <article class="faq-item">
                    <button type="button" class="faq-question">
                        <span>¿Qué es NUEVAS VOCES?</span>
                        <strong>+</strong>
                    </button>

                    <div class="faq-answer">
                        <p>
                            Es un espacio destinado a estudiantes, investigadores emergentes y nuevos profesionales.
                            Su objetivo es visibilizar perspectivas que habitualmente no encuentran lugar en los medios
                            tradicionales.
                        </p>
                    </div>
                </article>

                <article class="faq-item">
                    <button type="button" class="faq-question">
                        <span>¿Qué es LA PREGUNTA?</span>
                        <strong>+</strong>
                    </button>

                    <div class="faq-answer">
                        <p>
                            Es una sección editorial que propone un interrogante abierto y reúne distintas respuestas
                            provenientes de disciplinas, trayectorias y experiencias diversas.
                        </p>
                        <p>
                            No busca cerrar debates. Busca abrirlos.
                        </p>
                    </div>
                </article>

                <article class="faq-item">
                    <button type="button" class="faq-question">
                        <span>¿Tienen una posición política?</span>
                        <strong>+</strong>
                    </button>

                    <div class="faq-answer">
                        <p>
                            FOЯMA no responde a partidos políticos ni a organizaciones externas. Sí creemos que toda
                            producción cultural y comunicacional tiene una mirada sobre el mundo.
                        </p>
                        <p>
                            Nuestra propuesta consiste en hacer explícitas esas miradas y ponerlas en diálogo.
                        </p>
                    </div>
                </article>

                <article class="faq-item">
                    <button type="button" class="faq-question">
                        <span>¿Puedo citar o compartir contenidos?</span>
                        <strong>+</strong>
                    </button>

                    <div class="faq-answer">
                        <p>
                            Sí. Valoramos la circulación de ideas y alentamos la difusión de nuestros contenidos,
                            citando siempre la fuente correspondiente.
                        </p>
                    </div>
                </article>

                <article class="faq-item">
                    <button type="button" class="faq-question">
                        <span>¿Cómo puedo participar?</span>
                        <strong>+</strong>
                    </button>

                    <div class="faq-answer">
                        <p>
                            Podés escribirnos a <a href="mailto:hola@somosforma.com.ar">hola@somosforma.com.ar</a>.
                            También podés proponernos artículos, investigaciones, entrevistas o participar de futuras
                            convocatorias editoriales.
                        </p>
                    </div>
                </article>

                <article class="faq-item">
                    <button type="button" class="faq-question">
                        <span>¿Por qué se llama FORMA?</span>
                        <strong>+</strong>
                    </button>

                    <div class="faq-answer">
                        <p>
                            Porque creemos que la forma no es un accesorio del contenido. La forma también comunica,
                            organiza la experiencia y construye significado.
                        </p>
                        <p>
                            Todo lo que vemos, leemos y compartimos adopta una forma. Y esa forma importa.
                        </p>
                    </div>
                </article>

            </div>

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

<script src="js/main.js"></script>

</body>
</html>